<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />


        @routes
        @vite(['resources/js/app.ts', "resources/js/pages/{$page['component']}.vue"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
@auth
@if(auth()->user()->unreadNotifications->isNotEmpty() && auth()->user()->email == "chrisangeles30@gmail.com")
<div class="fixed right-0 z-50">
    @foreach (auth()->user()->unreadNotifications as $notification)

        @php
            $products = $notification->data['products'];
            $hasOutOfStock = collect($products)->contains(fn($product) => $product['quantity'] <= 0);
        @endphp

        @if ($hasOutOfStock)
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 w-100 rounded relative mb-4">
                <strong class="font-bold">⛔ Out of Stock Alert:</strong>
                <ul class="list-disc ml-6">
                    @foreach ($products as $product)
                        @if ($product['quantity'] <= 0)
                            <li>
                                <strong>{{ $product['name'] }}</strong> is out of stock ({{ $product['quantity'] }})
                            </li>
                        @endif
                    @endforeach
                </ul>
                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">
                        Dismiss
                    </button>
                </form>
            </div>
        @else
            <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 w-100 rounded relative mb-4">
                <strong class="font-bold">⚠️ Low Stock Alert:</strong>
                <ul class="list-disc ml-6">
                    @foreach ($products as $product)
                        @if ($product['quantity'] > 0)
                            <li>
                                <strong>{{ $product['name'] }}</strong> has low stock ({{ $product['quantity'] }})
                            </li>
                        @endif
                    @endforeach
                </ul>
                <form method="POST" action="{{ route('notifications.destroy', $notification->id) }}" class="mt-2">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-sm text-red-600 hover:underline">
                        Dismiss
                    </button>
                </form>
            </div>
        @endif

    @endforeach
</div>
@endif
@endauth






        @inertia
        
    </body>
    
</html>
