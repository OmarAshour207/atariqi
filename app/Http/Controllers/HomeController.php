<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\HomepageStat;
use App\Models\PartnerAchievement;
use App\Models\Testimonial;

class HomeController extends Controller
{
    public function home()
    {
        return view('home');
    }

    public function support()
    {
        return view('support');
    }

    public function homepageSections()
    {
        $sections = HomepageSection::groupBy('section_key')->get();

        $data = [
            'about_us' => HomepageSection::where('section_key', 'about_us')->first(),
            'about_app' => HomepageSection::where('section_key', 'about_app')->first(),
            'stats' => HomepageStat::all(),
            'testimonials' => Testimonial::all(),
            'achievements' => PartnerAchievement::where('type', 'achievement')->get(),
            'partners' => PartnerAchievement::where('type', 'partner')->get(),
        ];

        return response()->json($data);
    }

}
