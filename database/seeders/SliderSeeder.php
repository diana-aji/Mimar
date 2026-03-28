<?php

namespace Database\Seeders;

use App\Models\Slider;
use Illuminate\Database\Seeder;

class SliderSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'title_ar' => 'ابدأ مشروعك العقاري بثقة ووضوح',
                'title_en' => 'Start your real estate project with confidence',
                'subtitle_ar' => 'استكشف الخدمات، احسب التكاليف، وتواصل مع مزودي الخدمة ضمن منصة واحدة حديثة ومنظمة.',
                'subtitle_en' => 'Browse services, estimate costs, and connect with providers in one modern platform.',
                'image' => 'https://images.unsplash.com/photo-1460317442991-0ec209397118?auto=format&fit=crop&w=1600&q=80',
                'button_text_ar' => 'ابدأ التقدير',
                'button_text_en' => 'Start Estimation',
                'button_url' => '/estimations/create',
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'title_ar' => 'خدمات احترافية لمختلف مراحل البناء والتشطيب',
                'title_en' => 'Professional services for every construction stage',
                'subtitle_ar' => 'اختر من بين مجموعة متنوعة من الخدمات المعتمدة وربطها مباشرة باحتياجات مشروعك.',
                'subtitle_en' => 'Choose from a wide range of approved services and match them directly to your project needs.',
                'image' => 'https://images.unsplash.com/photo-1505693416388-ac5ce068fe85?auto=format&fit=crop&w=1600&q=80',
                'button_text_ar' => 'استكشف الخدمات',
                'button_text_en' => 'Explore Services',
                'button_url' => '/services',
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'title_ar' => 'إدارة الطلبات والمتابعة من مكان واحد',
                'title_en' => 'Manage requests from one place',
                'subtitle_ar' => 'تابع الطلبات المرسلة والمستلمة، وراقب الحالة والتفاصيل بخطوات واضحة وسهلة.',
                'subtitle_en' => 'Track sent and received requests, monitor status, and manage details in one place.',
                'image' => 'https://images.unsplash.com/photo-1484154218962-a197022b5858?auto=format&fit=crop&w=1600&q=80',
                'button_text_ar' => 'عرض الطلبات',
                'button_text_en' => 'View Orders',
                'button_url' => '/orders',
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($items as $item) {
            Slider::query()->updateOrCreate(
                ['sort_order' => $item['sort_order']],
                $item
            );
        }
    }
}