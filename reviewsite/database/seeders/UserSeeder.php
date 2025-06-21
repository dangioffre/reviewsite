<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run(): void
    {
        // Create 15 staff members (admins) with realistic gaming-related names
        $staffNames = [
            'Alex GameMaster',
            'Sarah TechReviewer',
            'Mike PixelPro',
            'Jessica GameGuru',
            'David RetroGamer',
            'Emma PlayTester',
            'Ryan CodeCrafter',
            'Lisa GameDesigner',
            'Chris FrameRate',
            'Amy ControllerQueen',
            'Jake SpeedRunner',
            'Sophie GameCritic',
            'Matt HardwareHero',
            'Rachel IndieExpert',
            'Tom GraphicsGod'
        ];

        foreach ($staffNames as $name) {
            User::create([
                'name' => $name,
                'email' => strtolower(str_replace(' ', '.', $name)) . '@reviewsite.com',
                'password' => Hash::make('password'),
                'is_admin' => true,
                'email_verified_at' => now(),
            ]);
        }

        // Create 85 regular users with gaming-themed names
        $regularUserNames = [
            'NoobSlayer2023', 'PixelHunter', 'GameMaster99', 'RetroFan', 'SpeedRunner42',
            'ConsoleWarrior', 'PCMasterRace', 'IndieGamer', 'CasualPlayer', 'HardcoreGamer',
            'FrameRateKing', 'GraphicsGuru', 'SoundtrackLover', 'AchievementHunter', 'LootCollector',
            'QuestSeeker', 'BossBeater', 'ComboMaster', 'StrategyExpert', 'PuzzleSolver',
            'ActionHero', 'RPGFanatic', 'FPSPro', 'PlatformJumper', 'RacingDemon',
            'SimulationKing', 'SandboxBuilder', 'StorySeeker', 'MultiplayerMaven', 'SinglePlayerSage',
            'VRExplorer', 'MobileGamer', 'HandheldHero', 'ArcadeClassic', 'DigitalNomad',
            'PixelArtist', 'ChiptuneChamp', 'ModderMagic', 'StreamSniper', 'ContentCreator',
            'TwitchTurbo', 'YouTubeGamer', 'DiscordMod', 'RedditRanger', 'ForumFighter',
            'WikiWarrior', 'GuideGuru', 'TutorialTitan', 'ReviewRanger', 'CriticChamp',
            'ScoreKeeper', 'RatingRuler', 'TierListMaker', 'MetaCritic', 'GameJournalist',
            'IndieDev', 'GameTester', 'BugHunter', 'AlphaTester', 'BetaBuster',
            'EarlyAccess', 'DayOnePlayer', 'PatientGamer', 'SaleHunter', 'BundleBuyer',
            'CollectorEdition', 'DigitalDownload', 'PhysicalMedia', 'RetroCollector', 'VintageGamer',
            'NextGenGamer', 'TechEnthusiast', 'GadgetGuru', 'HardwareHacker', 'OverclockerPro',
            'CableManager', 'RGBMaster', 'SilentPC', 'WaterCooled', 'AirCooled',
            'TeamRed', 'TeamGreen', 'TeamBlue', 'NeutralGamer', 'BrandLoyal'
        ];

        foreach ($regularUserNames as $name) {
            User::create([
                'name' => $name,
                'email' => strtolower($name) . '@example.com',
                'password' => Hash::make('password'),
                'is_admin' => false,
                'email_verified_at' => now(),
            ]);
        }

        $this->command->info('Created 15 staff users and 85 regular users (100 total)');
    }
}
