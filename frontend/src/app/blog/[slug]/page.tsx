// app/blog/[slug]/page.tsx
import { Metadata } from "next";
import { blogService } from "@/services/blogService";
import { notFound } from "next/navigation";
import SingleBlogPage from "@/components/blog/SingleBlogPage";
import Layout from "@/components/layout/Layout";
import Image from "next/image";
import Link from "next/link";
import { baseUri } from "@/services/constant";

interface Props {
  params: {
    slug: string;
  };
}

// ✅ Generate metadata dynamically
export async function generateMetadata({ params }: Props): Promise<Metadata> {
  try {
    const response = await blogService.getBlogBySlug(params.slug);

    if (!response.success || response.data.length === 0) {
      return {
        title: "Blog Not Found",
        description: "The requested blog post could not be found.",
      };
    }

    const blog = response.data[0];
    const siteUrl =
      process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
      "https://realtimebiometrics.com";
    const blogUrl = `${siteUrl}/blog/${blog.slug}`;
    const metaTitle = `${blog.meta_title || blog.title} | Realtime Biometrics`;
    const metaDescription =
      blog.meta_description ||
      blog.excerpt ||
      (blog.content ? blog.content.replace(/<[^>]*>/g, "").slice(0, 160) : "");
    const ogImage = blog.featured_image ? `${baseUri}${blog.featured_image}` : undefined;

    return {
      title: metaTitle,
      description: metaDescription,
      keywords: blog.tags?.length ? blog.tags.join(", ") : undefined,
      authors: [{ name: blog.author.name }],
      alternates: { canonical: blogUrl },
      openGraph: {
        title: metaTitle,
        description: metaDescription,
        type: "article",
        url: blogUrl,
        siteName: "Realtime Biometrics",
        publishedTime: blog.published_at,
        authors: [blog.author.name],
        tags: blog.tags,
        images: ogImage ? [{ url: ogImage, alt: blog.title }] : undefined,
      },
      twitter: {
        card: "summary_large_image",
        title: metaTitle,
        description: metaDescription,
        images: ogImage ? [ogImage] : undefined,
      },
      robots: {
        index: true,
        follow: true,
      },
    };
  } catch {
    // ⚡ Ignore error silently but return fallback metadata
    return {
      title: "Blog Post",
      description: "Read this amazing blog post",
    };
  }
}

// ✅ Generate static paths for SSG
export async function generateStaticParams() {
  try {
    const response = await blogService.getPublishedBlogs({ per_page: 50 });

    // Unique slugs ke saath paths generate karo
    const uniqueSlugs = new Set<string>();
    const paths: { slug: string }[] = [];

    for (const blog of response.data) {
      if (blog.slug && !uniqueSlugs.has(blog.slug)) {
        uniqueSlugs.add(blog.slug);
        paths.push({ slug: blog.slug });
      }
    }

    return paths;
  } catch {
    // ⚡ Error ignore, empty list return
    return [];
  }
}

// ✅ Page component
export default async function BlogDetailPage({ params }: Props) {
  try {
    const response = await blogService.getBlogBySlug(params.slug);

    if (!response.success || response.data.length === 0) {
      notFound();
    }
    const blog = response.data[0];
    const siteUrl =
      process.env.NEXT_PUBLIC_SITE_URL?.replace(/\/+$/, "") ||
      "https://realtimebiometrics.com";
    const blogUrl = `${siteUrl}/blog/${blog.slug}`;
    const ogImage = blog.featured_image ? `${baseUri}${blog.featured_image}` : undefined;
    const metaDescription =
      blog.meta_description ||
      blog.excerpt ||
      (blog.content ? blog.content.replace(/<[^>]*>/g, "").slice(0, 160) : "");

    const latestRes = await blogService.getPublishedBlogs({ per_page: 6 });
    const latestAll = Array.isArray(latestRes?.data) ? latestRes.data : [];
    const latest = latestAll
      .filter((b) => b.slug !== blog.slug)
      .sort((a, b) => {
        const da = new Date(a.published_at || a.created_at).getTime();
        const db = new Date(b.published_at || b.created_at).getTime();
        return db - da;
      })
      .slice(0, 5);

    const articleLd = {
      "@context": "https://schema.org",
      "@type": "Article",
      headline: blog.title,
      description: metaDescription,
      image: ogImage ? [ogImage] : undefined,
      datePublished: blog.published_at,
      dateModified: blog.updated_at,
      author: { "@type": "Person", name: blog.author?.name },
      publisher: {
        "@type": "Organization",
        name: "Realtime Biometrics",
        logo: { "@type": "ImageObject", url: `${siteUrl}/images/logo.png` },
      },
      mainEntityOfPage: { "@type": "WebPage", "@id": blogUrl },
    };

    return (
      <Layout>
        <div className="container mx-auto px-4 py-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-6 lg:gap-8">
            <div className="lg:col-span-2">
              <SingleBlogPage blog={blog} />
            </div>
            <aside className="space-y-4 lg:sticky lg:top-28 lg:self-start">
              <h2 className="text-lg md:text-xl font-semibold text-gray-900">Latest Posts</h2>

              <div className="md:hidden">
                <div className="grid grid-cols-2 gap-3">
                  {latest.map((post) => (
                    <Link key={post.id} href={`/blog/${post.slug}`} className="group">
                      <div className="relative w-full h-24 rounded-lg overflow-hidden border border-gray-200">
                        <Image
                          src={post.featured_image ? `${baseUri}${post.featured_image}` : "/images/blog1.png"}
                          alt={post.title}
                          fill
                          className="object-cover"
                          unoptimized
                        />
                      </div>
                      <p className="mt-2 text-xs font-medium text-gray-900 line-clamp-2 group-hover:text-orange-600 transition-colors">{post.title}</p>
                      <p className="text-[10px] text-gray-500">{new Date(post.published_at || post.created_at).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</p>
                    </Link>
                  ))}
                </div>
              </div>

              <div className="hidden md:block space-y-4">
                {latest.map((post) => (
                  <Link key={post.id} href={`/blog/${post.slug}`} className="flex items-center gap-3 group">
                    <div className="relative w-16 h-16 rounded-lg overflow-hidden border border-gray-200 flex-shrink-0">
                      <Image
                        src={post.featured_image ? `${baseUri}${post.featured_image}` : "/images/blog1.png"}
                        alt={post.title}
                        fill
                        className="object-cover"
                        unoptimized
                      />
                    </div>
                    <div className="min-w-0">
                      <p className="text-sm font-medium text-gray-900 line-clamp-2 group-hover:text-orange-600 transition-colors">{post.title}</p>
                      <p className="text-xs text-gray-500">{new Date(post.published_at || post.created_at).toLocaleDateString("en-US", { month: "short", day: "numeric", year: "numeric" })}</p>
                    </div>
                  </Link>
                ))}
              </div>
            </aside>
          </div>
          <script type="application/ld+json" dangerouslySetInnerHTML={{ __html: JSON.stringify(articleLd) }} />
        </div>
      </Layout>
    );
  } catch {
    notFound();
  }
}
