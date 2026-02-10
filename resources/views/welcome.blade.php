<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Posts</title>
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                font-family: "Helvetica Neue", Arial, sans-serif;
                color: #1f2937;
                background: #faf7ff;
            }

            .page {
                max-width: 1100px;
                margin: 32px auto;
                padding: 0 16px 40px;
                display: flex;
                gap: 24px;
            }

            .sidebar {
                width: 220px;
                background: #ffffff;
                border: 1px solid #e9e3f7;
                border-radius: 10px;
                padding: 16px;
                height: fit-content;
            }

            .sidebar h2 {
                font-size: 16px;
                margin: 0 0 12px;
            }

            .category-list {
                list-style: none;
                padding: 0;
                margin: 0;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .category-link {
                display: block;
                padding: 8px 10px;
                border-radius: 6px;
                text-decoration: none;
                color: #1f2937;
                background: #f2ecfb;
            }

            .category-link.active {
                background: #5b21b6;
                color: #ffffff;
            }

            .content {
                flex: 1;
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .content h1 {
                font-size: 22px;
                margin: 0;
            }

            .cards {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 16px;
            }

            .card {
                background: #ffffff;
                border: 1px solid #e9e3f7;
                border-radius: 10px;
                padding: 14px;
                display: flex;
                flex-direction: column;
                gap: 8px;
            }

            .card-title {
                font-weight: 600;
                font-size: 16px;
            }

            .card-meta {
                font-size: 12px;
                color: #6b5d7a;
            }

            .empty {
                background: #ffffff;
                border: 1px dashed #d9ccf2;
                border-radius: 10px;
                padding: 16px;
                color: #6b5d7a;
            }

            @media (max-width: 760px) {
                .page {
                    flex-direction: column;
                }

                .sidebar {
                    width: 100%;
                }
            }
        </style>
    </head>
    <body>
        @php
            $activeId = $activeCategory?->id;
        @endphp

        <div class="page">
            <aside class="sidebar">
                <h2>Categories</h2>
                <ul class="category-list">
                    <li>
                        <a class="category-link {{ $activeId === null ? 'active' : '' }}" href="/">All Posts</a>
                    </li>
                    @foreach ($categories as $category)
                        <li>
                            <a
                                class="category-link {{ $activeId === $category->id ? 'active' : '' }}"
                                href="/?category={{ $category->id }}"
                            >
                                {{ $category->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </aside>

            <section class="content">
                <h1>{{ $activeCategory?->name ?? 'All Posts' }}</h1>

                @if ($posts->isEmpty())
                    <div class="empty">No posts found for this category.</div>
                @else
                    <div class="cards">
                        @foreach ($posts as $post)
                            <article class="card">
                                <div class="card-title">{{ $post->title }}</div>
                                <div class="card-meta">{{ $post->category?->name }}</div>
                                <p>{{ $post->description }}</p>
                            </article>
                        @endforeach
                    </div>
                @endif
            </section>
        </div>
    </body>
</html>
