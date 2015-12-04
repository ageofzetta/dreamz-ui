<?php

use App\User;
use App\Education_level;
use App\Position;
use App\Department;
use App\Company;
use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Faker\Factory as Faker;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        $department1 = Department::find(1);
        $where1 = ['company' => $department1->company, 'boss' => 1];
        $position1 = Position::where($where1)->first();
        $department2 = Department::find(2);
        $where2 = ['company' => $department2->company, 'boss' => 1];
        $position2 = Position::where($where2)->first();

    	User::create([
    		'name' => 'Alejandro',
			'lastname' => 'Tapia',
    		'email' => 'ageofzetta@gmail.com',
    		'company' => $department1->company,
    		'department' => $department1->department_id,
    		'position' => $position1->position_id,
    		'password' => Hash::make('admin'),
			'active' => 1,
			'user_id' => $faker->uuid,

    	]);

        $karla_company = Company::first();
    	User::create([
    		'name' => 'Karla',
			'lastname' => 'Reyes',
    		'email' => 'kreyes@fabricadesoluciones.com',
    		'company' => $department2->company,
    		'department' => $department2->department_id,
    		'position' => $position2->position_id,
    		'password' => Hash::make('admin'),
			'active' => 1,
			'company' => $karla_company->company_id,
			'user_id' => $faker->uuid,

    	]);
        $i = 0;


        $companies = Company::all();

        foreach ($companies as $company) {
            
            $departments = Department::where('company','=', $company->company_id)->get();
            foreach ($departments as $department) {
            	$i = 0;
		        foreach (range(1, 10) as $user) {
	            	if(!$i){
	            		$position = Position::where('name', '=', 'boss')->where('company','=',$company->company_id)->first()->position_id;
	            	}else{
	            		$position = Position::where('name', '=', 'employee')->where('company','=',$company->company_id)->first()->position_id;
	            	}
		    		$i++;
		        	User::create([
		        		'name' => $faker->firstName($gender = null|'male'|'female'),
		        		'email' => $faker->freeEmail,
		        		'password' => Hash::make($faker->word),
						'lastname' => $faker->lastName,
						'active' => $faker->boolean(70),
						'high_potential' => $faker->boolean(30),
						'company' => $company->company_id,
						'user_id' => $faker->uuid,
						'employee_number' => $faker->uuid,
						'department' => $department->department_id,
						'position' => $position,

						/*
			             TODO: Move to User Details

						'birth_date' => $faker->dateTimeBetween($startDate = '-60 years', $startDate = '-20 years'), 
						'education' => Education_level::find($faker->randomElement([1,2,3,4,5]))->education_level_id,
						'mobile' => $faker->phoneNumber,
						'alergies' => $faker->realText($maxNbChars = 200, $indexSize = 2),
						'blood_type' => $faker->randomElement($array = array ('A-', 'A+', 'B-', 'B+', 'AB-', 'A+B', 'O-', 'O+')),
						'emergency_contact' => $faker->name." ".$faker->phoneNumber,
						'phone' => $faker->phoneNumber,
						'admission_date' => $faker->dateTimeBetween($startDate = '-15 years', $startDate = '-1 years'), 
						'facebook' => "facebook.com/".$faker->userName,
						'twitter' => "twitter.com/@".$faker->userName,
						'instagram' => "instagram.com/".$faker->userName,
						'linkedin' => "linkedin.com/".$faker->userName,
						'googlep' => "googlep.com/".$faker->userName,

						*/
						/*
			             TODO: Move to User Assesments

						'd_s' => $faker->randomDigitNotNull,
						'i_s' => $faker->randomDigitNotNull,
						's_s' => $faker->randomDigitNotNull,
						'c_s' => $faker->randomDigitNotNull,
						'd_a' => $faker->randomDigitNotNull,
						'i_a' => $faker->randomDigitNotNull,
						's_a' => $faker->randomDigitNotNull,
						'c_a' => $faker->randomDigitNotNull,
						'welth_dynamics' => $faker->word,
						'stengths_finder_1' => $faker->word,
						'stengths_finder_2' => $faker->word,
						'stengths_finder_3' => $faker->word,
						'stengths_finder_4' => $faker->word,
						'stengths_finder_5' => $faker->word
						*/
		        	]);
		        }
            }
        }

        
    }
}


