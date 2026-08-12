'use client';

// Arbitrary HTML/JavaScript stored in database configuration is intentionally
// not executed. This closes a persistent-XSS path affecting every client page.
export default function GlobalCustomScripts() {
  return null;
}