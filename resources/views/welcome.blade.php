<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>型log</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="manifest" href="/site.webmanifest">

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-gray-100 text-gray-900 antialiased">
        <main class="min-h-screen flex items-center justify-center px-6">
            <section class="w-full max-w-2xl bg-white rounded-xl shadow p-8 sm:p-10">
                <h1 class="text-3xl font-bold">型log</h1>
                <p class="mt-3 text-gray-600 leading-relaxed">
                    モデルごとの実体験データを集めて、比較しやすくするためのログサービスです。
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    @auth
                        <a
                            href="{{ url('/dashboard') }}"
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            ダッシュボードへ
                        </a>
                    @else
                        <a
                            href="{{ route('login') }}"
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-medium text-white hover:bg-gray-800"
                        >
                            ログイン
                        </a>

                        @if (Route::has('register'))
                            <a
                                href="{{ route('register') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            >
                                新規登録
                            </a>
                        @endif
                    @endauth
                </div>
            </section>
        </main>
    </body>
</html>
