import { NextRequest, NextResponse } from "next/server";

export const dynamic = "force-dynamic";
export const runtime = "nodejs";
export const maxDuration = 60;

function backendOrigin(): string {
  const configured =
    process.env.BACKEND_URL ?? process.env.NEXT_PUBLIC_API_URL ?? "";

  return configured.replace(/\/api\/?$/, "").replace(/\/+$/, "");
}

function isAuthorized(request: NextRequest, secret: string): boolean {
  const authorization = request.headers.get("authorization");
  const bearer = authorization?.startsWith("Bearer ")
    ? authorization.slice(7)
    : "";

  return (
    bearer === secret ||
    request.headers.get("x-cron-secret") === secret ||
    request.nextUrl.searchParams.get("secret") === secret
  );
}

async function runOrderCron(request: NextRequest) {
  const secret = process.env.CRON_SECRET ?? "";
  if (!secret) {
    return NextResponse.json(
      { status: "error", message: "CRON_SECRET chưa được cấu hình trên Next.js." },
      { status: 503 },
    );
  }

  if (!isAuthorized(request, secret)) {
    return NextResponse.json(
      { status: "error", message: "Cron secret không hợp lệ." },
      { status: 401 },
    );
  }

  const origin = backendOrigin();
  if (!origin) {
    return NextResponse.json(
      { status: "error", message: "BACKEND_URL chưa được cấu hình." },
      { status: 503 },
    );
  }

  const target = new URL("/cron/order/status", origin);
  const providerId = request.nextUrl.searchParams.get("provider_id");
  if (providerId) {
    target.searchParams.set("provider_id", providerId);
  }

  try {
    const response = await fetch(target, {
      method: "GET",
      cache: "no-store",
      headers: {
        Accept: "application/json",
        "User-Agent": "SMMV2-Next-Cron/1.0",
      },
      signal: AbortSignal.timeout(55_000),
    });
    const body = await response.text();
    const contentType = response.headers.get("content-type") ?? "";

    if (contentType.includes("application/json")) {
      return new NextResponse(body, {
        status: response.status,
        headers: { "content-type": "application/json" },
      });
    }

    return NextResponse.json(
      {
        status: "error",
        message: "Backend cron không trả về JSON.",
        backend_status: response.status,
        response: body.slice(0, 1000),
      },
      { status: response.ok ? 502 : response.status },
    );
  } catch (error) {
    return NextResponse.json(
      {
        status: "error",
        message: "Không thể gọi backend cron.",
        error: error instanceof Error ? error.message : String(error),
      },
      { status: 502 },
    );
  }
}

export async function GET(request: NextRequest) {
  return runOrderCron(request);
}

export async function POST(request: NextRequest) {
  return runOrderCron(request);
}
