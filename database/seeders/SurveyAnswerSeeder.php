<?php

namespace Database\Seeders;

use App\Models\StoreName;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class SurveyAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('survey_answers')->delete();  // Delete all records

        $faker = Faker::create();
        $data = [];

        // Generate data for each user
        for ($i = 0; $i < 2000; $i++) {
            $claimStatus = $faker->randomElement(['claimed', 'not_yet', 'expired']);
            $claimByUserId = null;
            $claimedDate = null;

            // If the claim is not yet or expired, set claim_by_user_id and claimed_date to null
            if ($claimStatus === 'claimed') {
                $claimByUserId = 2; // Fetch a random user ID
                $claimedDate = now();
            }

            $data[] = [
                'receipt_number' => Str::random(10),
                'store_id' => StoreName::inRandomOrder()->value('id'),
                'first_name' => $faker->firstName,
                'middle_name' => $faker->firstName,
                'last_name' => $faker->lastName,
                'suffix' => $faker->suffix,
                'mobile_number' => '+63' . $faker->unique()->numerify('9#########'),
                'mobile_number_verified' => 1,
                'gender' => $faker->randomElement(['male', 'female']),
                'birthday' => $faker->dateTimeBetween('-70 years', '-18 years')->format('Y-m-d'),
                'questionnaire_answer' => '[
                                            {
                                                "questions": [
                                                    {
                                                        "answer": "In-Store Visit",
                                                        "otherAnswer": null,
                                                        "questionName": "Which of the following best describes your visit?",
                                                        "questionType": "radio",
                                                        "questionOptions": [
                                                            "In-Store Visit",
                                                            "Fresh Options  Online Order",
                                                            "Store Order and Delivery",
                                                            "Advance Order and Pick Up"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            "Sample1"
                                                        ],
                                                        "questionName": "Please specify the store where you made your purchase?",
                                                        "questionType": "paragraph",
                                                        "questionOptions": []
                                                    },
                                                    {
                                                        "answer": [
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "Please rate your overall satisfaction with your experience at Fresh Options Meatshop"
                                                            }
                                                        ],
                                                        "questionName": null,
                                                        "questionType": "grid",
                                                        "questionOptions": [
                                                            "Highly Satisfied",
                                                            "Satisfied",
                                                            "Neither satisfied nor Dissatisfied",
                                                            "Dissatisfied",
                                                            "Highly Dissatisfied"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            "Chicken",
                                                            "Pork"
                                                        ],
                                                        "questionName": "During your visit at Fresh Options Meatshop, what did you purchase?",
                                                        "questionType": "checkbox",
                                                        "questionOptions": [
                                                            "Chicken",
                                                            "Pork",
                                                            "Beef",
                                                            "Ready-to-Heat (e.g Pork Sisig,  Pork Dinuguan, Bopis, etc.)",
                                                            "Ready-to-Cook (e.g Shanghai, Siomai, Quekiam, et.c)",
                                                            "Weekly Specials (e.g Chicken Karaage, Premium Pork Tonkatsu, etc..)",
                                                            "Breakfast (e.g Chicken Nuggets, Chicken Drumettes, Tocino, Longanisa, Patties, Hotdogs, Tapa, etc.)",
                                                            "Fresh Brown Eggs"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The accuracy of your order"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The speed of the service"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The freshness of the products"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The availability of the products you wanted"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The friendliness of the staff who served you"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The store cleanliness"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "Your experience during the payment process"
                                                            }
                                                        ],
                                                        "questionName": null,
                                                        "questionType": "grid",
                                                        "questionOptions": [
                                                            "Highly Satisfied",
                                                            "Satisfied",
                                                            "Neither Satisfied nor Dissatisfied",
                                                            "Dissatisfied",
                                                            "Highly Dissatisfied"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "Freshness & Quality"
                                                            },
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "The overall value for the price you paid"
                                                            }
                                                        ],
                                                        "questionName": null,
                                                        "questionType": "grid",
                                                        "questionOptions": [
                                                            "Highly Satisfied",
                                                            "Satisfied",
                                                            "Neither Satisfied nor Dissatisfied",
                                                            "Dissatisfied",
                                                            "Highly Dissatisfied"
                                                        ]
                                                    }
                                                ]
                                            },
                                            {
                                                "questions": [
                                                    {
                                                        "answer": "Yes",
                                                        "questionName": "Did you experience a problem during your visit?",
                                                        "questionType": "dropdown",
                                                        "questionOptions": [
                                                            "Yes",
                                                            "No"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            "The accuracy of your order"
                                                        ],
                                                        "questionName": "If Yes, please select the type of problem your experienced",
                                                        "questionType": "checkbox",
                                                        "questionOptions": [
                                                            "The accuracy of your order",
                                                            "The speed of the service",
                                                            "The freshness of the products",
                                                            "The availability of the products you wanted",
                                                            "The friendliness of the staff who served you",
                                                            "The store cleanliness",
                                                            "Your experience during the payment process"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            {
                                                                "rowAnswer": "Highly Satisfied",
                                                                "rowQuestion": "Please rate your satisfaction with how well the problem was resolved. If you did not bring the problem to an staff’s attention, select N/A."
                                                            }
                                                        ],
                                                        "questionName": null,
                                                        "questionType": "grid",
                                                        "questionOptions": [
                                                            "Highly Satisfied",
                                                            "Satisfied",
                                                            "Neither satisfied nor Dissatisfied",
                                                            "Dissatisfied",
                                                            "Highly Dissatisfied",
                                                            "N/A"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            {
                                                                "rowAnswer": "Highly Likely",
                                                                "rowQuestion": "Based on this visit, what is the likelihood that you will return to this Fresh Options Meatshop in the next 30 days or on your next visit?"
                                                            }
                                                        ],
                                                        "questionName": null,
                                                        "questionType": "grid",
                                                        "questionOptions": [
                                                            "Highly Likely",
                                                            "Likely",
                                                            "Somewhat Likely",
                                                            "Not Very Likely",
                                                            "Not At All Likely"
                                                        ]
                                                    },
                                                    {
                                                        "answer": [
                                                            "Sample2"
                                                        ],
                                                        "questionName": "Please tell us why you were not Highly Satisfied with your experience at this Fresh Options Meatshop. Be specific as you would like.",
                                                        "questionType": "paragraph",
                                                        "questionOptions": []
                                                    }
                                                ]
                                            },
                                            {
                                                "questions": [
                                                    {
                                                        "answer": [
                                                            "Meat quality and freshness",
                                                            "Value for money pricing",
                                                            "Variety of options",
                                                            "Online Ordering and Delivery"
                                                        ],
                                                        "questionName": "What was your primary reason to purchase your meat at Fresh Options Meatshop?(Select all that apply)",
                                                        "questionType": "checkbox",
                                                        "questionOptions": [
                                                            "Meat quality and freshness",
                                                            "Value for money pricing",
                                                            "Variety of options",
                                                            "Online Ordering and Delivery",
                                                            "Friendly and Knowledgeable Staff",
                                                            "Special Discounts and Promotions",
                                                            "Products always available",
                                                            "Customization/Cutting Options",
                                                            "Clean and Hygienic Store Environment",
                                                            "Trustworthiness and reliability of the meats hop",
                                                            "Recommendations from friends or family",
                                                            "Convenient Location",
                                                            "Payment options",
                                                            "Received an email from Fresh Options Meatshop",
                                                            "Thru Facebook, Instagram or other Social Media Ads"
                                                        ]
                                                    }
                                                ]
                                            }
                                        ]',
                'voucher_code' => Str::random(15),
                'valid_until' => $faker->dateTimeBetween('now', '+1 month')->format('Y-m-d'),
                'claim' => $claimStatus,
                'claim_by_user_id' => $claimByUserId,
                'claimed_date' => $claimedDate,
                'is_active' => $faker->boolean(90),
                'submit_date' => $faker->dateTimeBetween('-1 month', 'now')->format('Y-m-d'),
                'created_at' => now(),
                'updated_at' => now(),
                'deleted_at' => null,
            ];
        }

        // Insert the data in chunks for performance
        $chunks = array_chunk($data, 5000);
        foreach ($chunks as $chunk) {
            DB::table('survey_answers')->insert($chunk); // Replace 'your_table_name' with the actual table name
        }
    }
}
