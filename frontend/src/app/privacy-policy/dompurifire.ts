const sanitizeHTML = (html: string): string => {
  const scriptTagRegex = /<script\b[^>]*>[\s\S]*?<\/script>/gi;
  const eventHandlerRegex = /on[a-z]+=\s*"[^"]*"/gi;
  const javascriptProtocolRegex = /href=\s*"javascript:[^"]*"/gi;
  const iframeTagRegex = /<iframe\b[^>]*>[\s\S]*?<\/iframe>/gi;
  const objectTagRegex = /<object\b[^>]*>[\s\S]*?<\/object>/gi;
  const embedTagRegex = /<embed\b[^>]*>[\s\S]*?<\/embed>/gi;

  const sanitized = html
    .replace(scriptTagRegex, "")
    .replace(eventHandlerRegex, "")
    .replace(javascriptProtocolRegex, "")
    .replace(iframeTagRegex, "")
    .replace(objectTagRegex, "")
    .replace(embedTagRegex, "");

  return sanitized;
};

const DOMPurify = {
  sanitize: sanitizeHTML,
};

export default DOMPurify;
