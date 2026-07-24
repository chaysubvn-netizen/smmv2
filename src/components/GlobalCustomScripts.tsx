'use client';

import { useEffect, useState } from 'react';
import { usePathname } from 'next/navigation';

type Scripts = { script_header?: string | null; script_body?: string | null; script_footer?: string | null };

function mountHtml(html: string, target: HTMLElement, position: 'start' | 'end') {
  const template = document.createElement('template');
  template.innerHTML = html;
  const nodes: ChildNode[] = [];

  for (const source of Array.from(template.content.childNodes)) {
    if (source.nodeName.toLowerCase() === 'script') {
      const original = source as HTMLScriptElement;
      const script = document.createElement('script');
      for (const attribute of Array.from(original.attributes)) script.setAttribute(attribute.name, attribute.value);
      script.textContent = original.textContent;
      nodes.push(script);
    } else {
      nodes.push(source.cloneNode(true) as ChildNode);
    }
  }

  if (position === 'start') target.prepend(...nodes); else target.append(...nodes);
  return nodes;
}

export default function GlobalCustomScripts() {
  const pathname = usePathname();
  const [scripts, setScripts] = useState<Scripts>({});

  useEffect(() => {
    if (pathname.startsWith('/admin')) return;
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://127.0.0.1:8000/api';
    fetch(`${apiUrl}/client/config`, { headers: { Accept: 'application/json' } })
      .then(response => response.ok ? response.json() : Promise.reject(new Error('config')))
      .then(response => { if (response?.status) setScripts(response.data || {}); })
      .catch(() => undefined);
  }, [pathname]);

  useEffect(() => {
    if (pathname.startsWith('/admin')) return;
    const mounted: ChildNode[] = [];
    if (scripts.script_header?.trim()) mounted.push(...mountHtml(scripts.script_header, document.head, 'end'));
    if (scripts.script_body?.trim()) mounted.push(...mountHtml(scripts.script_body, document.body, 'start'));
    if (scripts.script_footer?.trim()) mounted.push(...mountHtml(scripts.script_footer, document.body, 'end'));
    return () => mounted.forEach(node => node.remove());
  }, [pathname, scripts.script_header, scripts.script_body, scripts.script_footer]);

  return null;
}
