<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Breed;
use App\Models\PetTypes;

class BreedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dog = PetTypes::where('name', 'Dog')->first();
        $cat = PetTypes::where('name', 'Cat')->first();

        $dogBreeds = [
            'Aspin', 'Basset Hound', 'Beagle', 'Belgian Malinois', 'Border Collie',
            'Boston Terrier', 'Boxer', 'Bulldog', 'Cane Corso', 'Chihuahua',
            'Cocker Spaniel', 'Corgi', 'Dalmatian', 'Doberman', 'French Bulldog',
            'German Shepherd', 'Golden Retriever', 'Great Dane', 'Jack Russell Terrier',
            'Labrador Retriever', 'Maltese', 'Miniature Pinscher', 'Pomeranian',
            'Poodle', 'Pug', 'Rottweiler', 'Samoyed', 'Schnauzer', 'Shiba Inu',
            'Shih Tzu', 'Siberian Husky', 'Yorkshire Terrier',
        ];

        
        foreach ($dogBreeds as $breed) {
            Breed::create(['pet_types_id' => $dog->id, 'name' => $breed]);
        }

        $catBreeds = [
            'American Curl', 'American Shorthair', 'Bengal', 'Birman',
            'Bombay', 'British Shorthair', 'Burmese', 'Devon Rex',
            'Exotic Shorthair', 'Himalayan', 'Maine Coon', 'Munchkin',
            'Norwegian Forest', 'Oriental Shorthair', 'Persian',
            'Ragdoll', 'Russian Blue', 'Scottish Fold',
            'Siamese', 'Sphynx',];

        foreach ($catBreeds as $breed) {
            Breed::create(['pet_types_id' => $cat->id, 'name' => $breed]);
        }
    }
}
