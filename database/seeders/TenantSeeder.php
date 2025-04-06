<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // DB::table('users')->insert([
        //     'name' => 'tenant',
        //     'email' => 'tenant@tenant.com',
        //     'password' => Hash::make('123456'),
        //     'role' => 'tenant',
        //     'user_code' => 'hp_usk_1'
        // ]);

        $data = [
                    [
                        'name' => 'USK',
                        'email' => 'usk@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('PEg6gnbJe7yt36F4'),
                        'role' => 'tenant',
                        'user_code' => 'hp_usk_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UNY',
                        'email' => 'uny@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('Jm8EtaCe8qh6Fbns'),
                        'role' => 'tenant',
                        'user_code' => 'hp_uny_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'INSTIKI',
                        'email' => 'instiki@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('8EB46k4DKBKqE8oL'),
                        'role' => 'tenant',
                        'user_code' => 'hp_instiki_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UNJAYA',
                        'email' => 'unjaya@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('397YCBn8hseTfPyX'),
                        'role' => 'tenant',
                        'user_code' => 'hp_unjaya_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'TELKOM',
                        'email' => 'telkom@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('s7kqiabT8B6$@GnH'),
                        'role' => 'tenant',
                        'user_code' => 'hp_telkom_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'IIBD',
                        'email' => 'iibd@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('qRG3PzDd8rC4X@CM'),
                        'role' => 'tenant',
                        'user_code' => 'hp_iibd_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'ITS',
                        'email' => 'its@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('Ld7JKDiH57nJq#J@'),
                        'role' => 'tenant',
                        'user_code' => 'hp_its_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UGM',
                        'email' => 'ugm@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('eq&zEKg4rJ5gs7BE'),
                        'role' => 'tenant',
                        'user_code' => 'hp_ugm_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UII',
                        'email' => 'uii@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('F$pcoDEhpniQ5FgC'),
                        'role' => 'tenant',
                        'user_code' => 'hp_uii_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UNNES',
                        'email' => 'unnes@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('$hG8H$m8!CN!sL#H'),
                        'role' => 'tenant',
                        'user_code' => 'hp_unnes_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UBJ',
                        'email' => 'ubj@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('8@J4CBBz9yoDYqfN'),
                        'role' => 'tenant',
                        'user_code' => 'hp_ubj_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'PSSN',
                        'email' => 'pssn@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('X&Jq6g5La4cG9zij'),
                        'role' => 'tenant',
                        'user_code' => 'hp_pssn_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UBD',
                        'email' => 'ubd@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('jai&5a9xXxbQ$dcH'),
                        'role' => 'tenant',
                        'user_code' => 'hp_ubd_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'OVH',
                        'email' => 'ovh@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('AFahbsRxNz#FC9se'),
                        'role' => 'tenant',
                        'user_code' => 'hp_ovh_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UNM',
                        'email' => 'unm@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('?nMSD$?S@9@cJ65f'),
                        'role' => 'tenant',
                        'user_code' => 'hp_unm_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UPN',
                        'email' => 'upn@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('qMQNPpCs6@sR8b@&'),
                        'role' => 'tenant',
                        'user_code' => 'hp_upnvj_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UBP',
                        'email' => 'ubp@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('EhRm@GtxtJ##5Cc7'),
                        'role' => 'tenant',
                        'user_code' => 'hp_ubp_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'ATMAJAYA',
                        'email' => 'atmajaya@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('sCqK43tFbFj75PN5'),
                        'role' => 'tenant',
                        'user_code' => 'hp_atmajaya_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'UPB',
                        'email' => 'upb@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('wdnmGlBWEKeuN'),
                        'role' => 'tenant',
                        'user_code' => 'hp_upb_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],
                    [
                        'name' => 'SGU',
                        'email' => 'sgu@cscisac.org',
                        'email_verified_at' => Carbon::now(),
                        'password' => Hash::make('ZokQhWss7UXDBxy'),
                        'role' => 'tenant',
                        'user_code' => 'hp_sgu_1',
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ],

                ];

                foreach ($data as $item) {
                    DB::table('users')->insert([
                        'name' => $item['name'],
                        'email' => $item['email'],
                        'email_verified_at' => $item['email_verified_at'],
                        'password' => $item['password'],
                        'role' => $item['role'],
                        'user_code' => $item['user_code'],
                        'created_at' => $item['created_at'],
                        'updated_at' => $item['updated_at'],

                    ]);
                }
    }
}
