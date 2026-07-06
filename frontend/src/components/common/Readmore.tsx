"use client";

import { useState } from "react";
interface ReadMoreProps {
  text?: string;
}

export default function ReadMore({ text }: ReadMoreProps) {
  const [expanded, setExpanded] = useState(false);

  if (!text) return null;

  const words = text.trim().split(/\s+/);
  const isLong = words.length > 10;
  const content = expanded ? text : words.slice(0, 20).join(" ");

  return (
    <p className="text-gray-700 leading-relaxed">
      {content}
      {isLong && !expanded && "... "}
      {isLong && (
        <span
          onClick={() => setExpanded(!expanded)}
          className="text-orange-600 cursor-pointer ml-1 text-sm font-light"
        >
          {expanded ? "Read Less" : "Read More"}
        </span>
      )}
    </p>
  );
}
