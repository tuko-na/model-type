<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>型log</title>
        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="manifest" href="/site.webmanifest">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div>
                <a href="/">
                    <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
                </a>
            </div>

            <div class="w-full sm:max-w-2xl mt-6 px-6">
                <h1 class="text-xl font-semibold text-gray-900">
                    「比べる基準」を変えるレビュー体験へ
                </h1>
                <p class="mt-3 text-sm text-gray-700 leading-relaxed">
                    ただの星評価ではなく、安心して長く使えるかどうかを見極めたい。
                    そんな視点で、一般層にもわかりやすい情報の整理を目指しています。
                </p>
                <div class="mt-4 text-sm text-gray-700 leading-relaxed">
                    <p>
                        1. 実際に使われた環境での“生存率”に注目し、カタログスペックだけでは見えない信頼性を伝えたい。
                    </p>
                    <p class="mt-2">
                        2. 好き嫌いの感想よりも、「何が起きたか」という事実を重ねて、比較しやすい基準をつくりたい。
                    </p>
                    <p class="mt-2">
                        3. 名前や色に左右されない整理で、同じ機械としての違いが見えるようにしたい。
                    </p>
                </div>
                <p class="mt-4 text-xs text-gray-500">
                    まだ構想段階のため、内容は今後アップデートしていきます。
                </p>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
