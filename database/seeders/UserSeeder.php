<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * Data anggota UKM Unit Usaha Mahasiswa
     */
    public function run(): void
    {
        // Ensure roles exist
        $adminRole = Role::firstOrCreate(['name' => 'administrator']);
        $pengurusRole = Role::firstOrCreate(['name' => 'pengurus']);
        $anggotaRole = Role::firstOrCreate(['name' => 'anggota']);

        // UKM Unit Usaha Mahasiswa Members Data
        $members = [
            // Ketua & Wakil Ketua - Administrator
            [
                'nim' => '220040265',
                'name' => 'I Made Wahyu Adi Putra',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Ketua Umum',
                'role' => 'administrator',
            ],
            [
                'nim' => '220040239',
                'name' => 'Putu Dharma Prima Andika',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Wakil Ketua Umum',
                'role' => 'administrator',
            ],

            // Sekretaris & Bendahara - Staff
            [
                'nim' => '240050124',
                'name' => 'Cathalina Maria Rosari',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Sekretaris 1',
                'role' => 'pengurus',
            ],
            [
                'nim' => '240030024',
                'name' => 'Gede Dirandra Satya Mahayana',
                'prodi' => 'Sistem Informasi',
                'jabatan' => 'Sekretaris 2',
                'role' => 'pengurus',
            ],
            [
                'nim' => '240050119',
                'name' => 'Ni Putu Dian Wedania Putri',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Bendahara 1',
                'role' => 'pengurus',
            ],
            [
                'nim' => '240050065',
                'name' => 'Ni Komang Kiera Dinda Arliana',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Bendahara 2',
                'role' => 'pengurus',
            ],

            // Koordinator Bidang - Staff
            [
                'nim' => '240050036',
                'name' => 'Ni Wayan Dwita Yani',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Koordinator Bidang 1',
                'role' => 'pengurus',
            ],
            [
                'nim' => '240040004',
                'name' => 'Kadek Aditya Anka Putra',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Koordinator Bidang 2',
                'role' => 'pengurus',
            ],
            [
                'nim' => '220040253',
                'name' => 'I Made Fadli Marantika',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Koordinator Bidang 3',
                'role' => 'pengurus',
            ],

            // Anggota Bidang 1 - User
            [
                'nim' => '240050035',
                'name' => 'Cokorda Istri Kanya',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 1',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050040',
                'name' => 'Maya Candra Pertiwi',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 1',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050127',
                'name' => 'Ni Komang Sugiantari',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 1',
                'role' => 'anggota',
            ],

            // Anggota Bidang 2 - User
            [
                'nim' => '220040252',
                'name' => 'I Komang Aditya Mas Wirawan',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Anggota Bidang 2',
                'role' => 'anggota',
            ],
            [
                'nim' => '240040025',
                'name' => 'Putu Ghana Dhirendra Redanayasa',
                'prodi' => 'Teknologi Informasi',
                'jabatan' => 'Anggota Bidang 2',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050202',
                'name' => 'Ketut Larashati Widiaswari',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 2',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050229',
                'name' => 'Ni Wayan Aning Wahyuningsih',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 2',
                'role' => 'anggota',
            ],

            // Anggota Bidang 3 - User
            [
                'nim' => '240030021',
                'name' => 'I Putu Nathan Verlianta Candra',
                'prodi' => 'Sistem Informasi',
                'jabatan' => 'Anggota Bidang 3',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050089',
                'name' => 'Kadek Bayu Wiryawan',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 3',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050116',
                'name' => 'I Gede Wahyu Weweka Nanda',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 3',
                'role' => 'anggota',
            ],
            [
                'nim' => '240050129',
                'name' => 'I Made Yoga Sandi Krisnawan',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota Bidang 3',
                'role' => 'anggota',
            ],

            // Anggota Baru - User
            [
                'nim' => '2200300623',
                'name' => 'I Gusti Ngurah Putu Mario Aryanga',
                'prodi' => 'Sistem Informasi',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2200500212',
                'name' => 'I Gusti Ayu Gede Sudha',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500038',
                'name' => 'Ni Nyoman Sinta Santorini',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500040',
                'name' => 'Ni Komang Ayu Widya Ningsih',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500044',
                'name' => 'Ni Kadek Santhi Widari',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500068',
                'name' => 'I Gede Ariswata',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500030',
                'name' => 'I Putu Ryan Tiadiadi',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500075',
                'name' => 'Putu Axu Binandri Arvanti Putri',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500090',
                'name' => 'Komang Sutini',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500103',
                'name' => 'Ni Ketut Tiara Asisteni',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500162',
                'name' => 'Ni Komang Ardilla Prawesti',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],

            // Anggota Baru - Anggota
            [
                'nim' => '2200300623',
                'name' => 'I Gusti Ngurah Putu Mario Aryanga',
                'prodi' => 'Sistem Informasi',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2200500212',
                'name' => 'I Gusti Ayu Gede Sudha',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500038',
                'name' => 'Ni Nyoman Sinta Santorini',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500040',
                'name' => 'Ni Komang Ayu Widya Ningsih',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500044',
                'name' => 'Ni Kadek Santhi Widari',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2300500068',
                'name' => 'I Gede Ariswata',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500030',
                'name' => 'I Putu Ryan Tiadiadi',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500075',
                'name' => 'Putu Axu Binandri Arvanti Putri',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500090',
                'name' => 'Komang Sutini',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500103',
                'name' => 'Ni Ketut Tiara Asisteni',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
            [
                'nim' => '2400500162',
                'name' => 'Ni Komang Ardilla Prawesti',
                'prodi' => 'Bisnis Digital',
                'jabatan' => 'Anggota',
                'role' => 'anggota',
            ],
        ];

        foreach ($members as $memberData) {
            $email = $memberData['nim'] . '@stikom.ac.id';
            
            // Check if user already exists
            $user = User::where('email', $email)->first();
            
            if (!$user) {
                $user = User::create([
                    'name' => $memberData['name'],
                    'email' => $email,
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ]);
            } else {
                // Update name if user exists but name is different
                if ($user->name !== $memberData['name']) {
                    $user->update(['name' => $memberData['name']]);
                }
            }

            // Assign role based on jabatan (sync roles to ensure correct role)
            switch ($memberData['role']) {
                case 'administrator':
                    $user->syncRoles([$adminRole]);
                    break;
                case 'pengurus':
                    $user->syncRoles([$pengurusRole]);
                    break;
                default:
                    $user->syncRoles([$anggotaRole]);
                    break;
            }
        }

        $this->command->info('Created ' . count($members) . ' UKM members successfully!');
    }
}
