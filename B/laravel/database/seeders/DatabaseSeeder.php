<?php

namespace Database\Seeders;

use App\Models\Hashtag;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    private const PLACEHOLDER_PNG_BASE64 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNk+A8AAQUBAScY42YAAAAASUVORK5CYII=';

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('profiles');
        Storage::disk('public')->makeDirectory('posts');

        $users = collect([
            ['name' => 'Aymen rachdi', 'username' => 'aymen_rachdi', 'email' => 'aymen@gmail.com'],
            ['name' => 'Ranya Bouzidi', 'username' => 'ranya_b', 'email' => 'ranya@gmail.com'],
            ['name' => 'Lina kamad', 'username' => 'lina_k', 'email' => 'lina@gmail.com'],
            ['name' => 'Youness EL-Dehbi', 'username' => 'youness_deb', 'email' => 'youness@gmail.com'],
            ['name' => 'Najoa Idrissi', 'username' => 'najoadd', 'email' => 'najoa@gmail.com'],
        ])->map(function (array $attrs) {
            return User::create([
                ...$attrs,
                'password' => Hash::make('password123'),
                'bio' => "Hi, I'm {$attrs['name']}!",
            ]);
        });

        $hashtagNames = [
            'technology', 'laravel', 'webdevelopment', 'travel', 'food',
            'photography', 'nature', 'fitness', 'music', 'art',
        ];
        $hashtags = collect($hashtagNames)->map(
            fn ($name) => Hashtag::create(['name' => $name])
        );

        $descriptions = [
            'Building a new Laravel API',
            'Exploring the mountains this weekend',
            'Fresh pasta made from scratch',
            'Golden hour never disappoints',
            'Morning run before the city wakes up',
            'New synth track in progress',
            'Sketching in the park today',
            'Refactoring old code, feels good',
            'Coffee and code, the perfect combo',
            'Sunset over the coast',
            'Trying out a new recipe tonight',
            'Deploying to production, wish me luck',
            'A quiet hike through the forest',
            'Studio session all afternoon',
            'Clean architecture matters',
            'Street photography walk downtown',
            'Weekend gym session complete',
            'Learning something new every day',
            'Testing the trending hashtag algorithm',
            'Wrapping up the sprint',
            'Painting with watercolors',
            'API design is an art form',
            'Beach day with friends',
            'Debugging production at 2am',
        ];

        $imagePath = $this->storePlaceholderImage('posts');

        $daysAgoCycle = [0, 0, 1, 1, 2, 3, 3, 4, 5, 6, 6, 7, 8, 9, 10, 11, 12, 13, 14, 14, 1, 2, 5, 8];

        $posts = collect();

        foreach ($descriptions as $index => $description) {
            $creator = $index % 3 === 0 ? $users[0] : $users->random();

            $post = Post::create([
                'user_id' => $creator->id,
                'description' => $description,
                'image' => $imagePath,
                'created_at' => now()->subDays($daysAgoCycle[$index] ?? 0)->subHours(random_int(0, 20)),
                'updated_at' => now(),
            ]);

            $tagCount = random_int(1, 3);
            $tags = $hashtags->random($tagCount)->pluck('id');
            $post->hashtags()->sync($tags);

            $posts->push($post);
        }

        foreach ($posts as $post) {
            $likers = $users->reject(fn ($u) => $u->id === $post->user_id)
                ->shuffle()
                ->take(random_int(0, 4));

            foreach ($likers as $liker) {
                $post->likes()->create(['user_id' => $liker->id]);
            }

            $post->update(['likes_count' => $post->likes()->count()]);
        }

        /*
            Test account credentials:
            Email: youness@gmail.com
            Password: password123
        */
    }

    private function storePlaceholderImage(string $dir): string
    {
        $filename = $dir.'/seed-placeholder-'.uniqid().'.png';
        Storage::disk('public')->put($filename, base64_decode(self::PLACEHOLDER_PNG_BASE64));

        return $filename;
    }
}
