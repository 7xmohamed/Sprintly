<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Laravel</title>
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-base-200">
    <div class="hero min-h-screen">
        <div class="hero-content text-center">
            <div class="max-w-md">
                <h1 class="text-5xl font-bold">Hello DaisyUI</h1>
                <p class="py-6">This is a test page with DaisyUI components</p>
                <div class="flex gap-2 justify-center">
                    <button class="btn btn-primary">Primary</button>
                    <button class="btn btn-secondary">Secondary</button>
                    <button class="btn btn-accent">Accent</button>
                </div>
                <div class="mt-4">
                    <div class="badge badge-primary">Primary</div>
                    <div class="badge badge-secondary">Secondary</div>
                    <div class="badge badge-accent">Accent</div>
                </div>
                <div class="mt-4">
                    <progress class="progress progress-primary w-56" value="70" max="100"></progress>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
