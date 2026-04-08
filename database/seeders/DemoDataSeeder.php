<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Media;
use App\Models\UserList;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario de prueba
        $user = User::firstOrCreate(
            ['email' => 'demo@myficlist.com'],
            [
                'name' => 'Usuario Demo',
                'password' => bcrypt('password')
            ]
        );

        // Crear algunos medios de prueba
        $animes = [
            [
                'external_id' => '1',
                'title' => 'Attack on Titan',
                'media_type' => 'anime',
                'source' => 'Jikan',
                'cover_url' => 'https://cdn.myanimelist.net/images/anime/10/47347.jpg',
                'synopsis' => 'Centuries ago, humanity was slaughtered to near extinction by monstrous creatures called Titans, forcing humans to hide in fear behind enormous bynames walls.',
                'extra_data' => json_encode([
                    'score' => 8.5,
                    'episodes' => 139,
                    'status' => 'Finished Airing',
                    'release_date' => 'Apr 7, 2013',
                    'type' => 'TV'
                ])
            ],
            [
                'external_id' => '2',
                'title' => 'Death Note',
                'media_type' => 'anime',
                'source' => 'Jikan',
                'cover_url' => 'https://cdn.myanimelist.net/images/anime/9/9453.jpg',
                'synopsis' => 'An intelligent high school student goes on a secret crusade to eliminate criminals from the world after discovering a notebook capable of killing anyone whose name is written into it.',
                'extra_data' => json_encode([
                    'score' => 8.6,
                    'episodes' => 37,
                    'status' => 'Finished Airing',
                    'release_date' => 'Oct 3, 2006',
                    'type' => 'TV'
                ])
            ],
            [
                'external_id' => '3',
                'title' => 'The Legend of Zelda: Breath of the Wild',
                'media_type' => 'game',
                'source' => 'RAWG',
                'cover_url' => 'https://media.rawg.io/media/games/b7a/b7a95ad4f8a0bef9b83f2ecbdf82cf0f.jpg',
                'synopsis' => 'An epic adventure to save a kingdom and a princess. Use bombs, arrows, and physics to solve puzzles in this open-world masterpiece.',
                'extra_data' => json_encode([
                    'metacritic' => 97,
                    'platforms' => ['Nintendo Switch', 'Wii U'],
                    'status' => 'Released'
                ])
            ],
            [
                'external_id' => '4',
                'title' => 'One Piece',
                'media_type' => 'anime',
                'source' => 'Jikan',
                'cover_url' => 'https://cdn.myanimelist.net/images/anime/6/73245.jpg',
                'synopsis' => 'Follows the adventures of Monkey D. Luffy and his pirate crew in order for him to obtain the treasure known as the One Piece to become the Pirate King.',
                'extra_data' => json_encode([
                    'score' => 8.5,
                    'episodes' => 1100,
                    'status' => 'Currently Airing',
                    'release_date' => 'Oct 20, 1999',
                    'type' => 'TV'
                ])
            ]
        ];

        foreach ($animes as $anime) {
            Media::firstOrCreate(
                ['external_id' => $anime['external_id'], 'source' => $anime['source']],
                $anime
            );
        }

        // Crear algunas entradas en la lista del usuario
        $mediaItems = Media::limit(4)->get();
        $statuses = ['watching', 'completed', 'plan_to_watch', 'dropped'];

        foreach ($mediaItems as $index => $media) {
            UserList::firstOrCreate(
                ['user_id' => $user->id, 'media_id' => $media->id],
                [
                    'status' => $statuses[$index % 4],
                    'score' => rand(6, 10),
                    'progress' => $index === 0 ? rand(5, 25) : 0
                ]
            );
        }
    }
}
