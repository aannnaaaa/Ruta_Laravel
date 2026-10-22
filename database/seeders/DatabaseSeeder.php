<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Post;
use App\Models\Profile;
use App\Models\RoutePoint;
use App\Models\Tag;
use App\Models\Trip;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Администратор',
            'email' => 'admin@ruta.ru',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        Profile::create(['user_id' => $admin->id, 'bio' => 'Администратор сервиса Ruta']);
        Profile::create(['user_id' => $user->id, 'bio' => 'Люблю путешествовать по Уралу и Сибири']);

        $tags = [];
        foreach ([
            'Горы' => 'gory',
            'Озёра' => 'ozera',
            'Город' => 'gorod',
            'Поход' => 'pohod',
            'Автопутешествие' => 'avto',
        ] as $name => $slug) {
            $tags[$slug] = Tag::create(['name' => $name, 'slug' => $slug]);
        }

        $trips = [
            [
                'user' => $user,
                'title' => 'Сургут — Ханты-Мансийск',
                'description' => 'Автомобильный маршрут по трассе вдоль Оби с остановками в посёлках.',
                'completed' => true,
                'tags' => ['avto', 'gorod'],
                'points' => [
                    ['Сургут', 'start', 61.25416700, 73.39638900],
                    ['Нефтеюганск', 'middle', 61.09888900, 72.60361100],
                    ['Ханты-Мансийск', 'end', 61.00305600, 69.01888900],
                ],
            ],
            [
                'user' => $user,
                'title' => 'Таганай за выходные',
                'description' => 'Пеший поход по национальному парку Таганай: Откликной гребень и Двуглавая сопка.',
                'completed' => false,
                'tags' => ['gory', 'pohod'],
                'points' => [
                    ['Златоуст', 'start', 55.17111100, 59.65083300],
                    ['Откликной гребень', 'middle', 55.23333300, 59.78333300],
                    ['Двуглавая сопка', 'end', 55.25000000, 59.80000000],
                ],
            ],
            [
                'user' => $admin,
                'title' => 'Озёра Южного Урала',
                'description' => 'Маршрут по озёрам Тургояк, Увильды и Зюраткуль.',
                'completed' => true,
                'tags' => ['ozera', 'avto'],
                'points' => [
                    ['Тургояк', 'start', 55.15000000, 60.06666700],
                    ['Увильды', 'middle', 55.51666700, 60.51666700],
                    ['Зюраткуль', 'end', 54.91666700, 59.20000000],
                ],
            ],
        ];

        foreach ($trips as $data) {
            $trip = Trip::create([
                'user_id' => $data['user']->id,
                'title' => $data['title'],
                'description' => $data['description'],
                'is_public' => true,
                'is_completed' => $data['completed'],
            ]);

            foreach ($data['points'] as $i => [$name, $type, $lat, $lng]) {
                RoutePoint::create([
                    'route_id' => $trip->id,
                    'name' => $name,
                    'type' => $type,
                    'lat' => $lat,
                    'lng' => $lng,
                    'order_index' => $i,
                ]);
            }

            $trip->tags()->attach(collect($data['tags'])->map(fn ($s) => $tags[$s]->id));
        }

        $posts = [
            [$user, 'Первый автопробег до Ханты-Мансийска', 'Дорога заняла около пяти часов, трасса хорошая, по пути отличные виды на Обь.', ['avto', 'gorod']],
            [$user, 'Как собрать рюкзак в поход на Таганай', 'Делюсь списком вещей: палатка, спальник, горелка, дождевик и запас воды.', ['gory', 'pohod']],
            [$admin, 'Добро пожаловать в Ruta!', 'Здесь можно планировать маршруты, делиться впечатлениями и находить попутчиков.', ['gorod']],
            [$admin, 'Лучшие озёра Урала для летнего отдыха', 'Тургояк называют младшим братом Байкала: вода в нём прозрачная на глубину до 18 метров.', ['ozera']],
        ];

        foreach ($posts as [$author, $title, $content, $postTags]) {
            $post = Post::create([
                'user_id' => $author->id,
                'title' => $title,
                'content' => $content,
            ]);

            $post->tags()->attach(collect($postTags)->map(fn ($s) => $tags[$s]->id));

            Comment::create([
                'post_id' => $post->id,
                'user_id' => $author->id === $admin->id ? $user->id : $admin->id,
                'text' => 'Отличный пост, спасибо!',
            ]);
        }
    }
}
