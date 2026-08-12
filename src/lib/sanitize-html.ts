const ALLOWED_TAGS = new Set(['p','br','b','strong','i','em','u','ul','ol','li','blockquote','code','pre','a']);

export function sanitizeHtml(input: unknown): string {
  let html = String(input ?? '');
  html = html.replace(/<!--[\s\S]*?-->/g, '');
  html = html.replace(/<(script|style|iframe|object|embed|svg|math|template|form|input|button|textarea|select|meta|link|base)[^>]*>[\s\S]*?<\/\1\s*>/gi, '');
  html = html.replace(/<(script|style|iframe|object|embed|svg|math|template|form|input|button|textarea|select|meta|link|base)\b[^>]*\/?\s*>/gi, '');
  return html.replace(/<\/?([a-z0-9-]+)([^>]*)>/gi, (tag, rawName: string, rawAttrs: string) => {
    const name = rawName.toLowerCase();
    if (!ALLOWED_TAGS.has(name)) return '';
    if (tag.startsWith('</')) return `</${name}>`;
    if (name === 'br') return '<br>';
    if (name !== 'a') return `<${name}>`;
    const match = rawAttrs.match(/\bhref\s*=\s*(?:"([^"]*)"|'([^']*)'|([^\s>]+))/i);
    const href = (match?.[1] ?? match?.[2] ?? match?.[3] ?? '').trim();
    if (!/^(https?:|mailto:|tel:|\/|#)/i.test(href)) return '<a>';
    const escaped = href.replace(/&/g, '&amp;').replace(/"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    return `<a href="${escaped}" rel="noopener noreferrer">`;
  });
}