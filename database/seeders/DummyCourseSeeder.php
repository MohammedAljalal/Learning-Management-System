<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Section;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DummyCourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get the instructor user (assuming ID 2 from logs, or fallback to first user)
        $instructor = User::find(2) ?? User::first();
        
        if (!$instructor) {
            $this->command->error('No user found to assign as instructor.');
            return;
        }

        // Ensure categories exist
        $categoriesData = [
            ['name' => 'البرمجة', 'slug' => 'programming'],
            ['name' => 'التصميم', 'slug' => 'design'],
            ['name' => 'الأعمال', 'slug' => 'business'],
            ['name' => 'اللغات', 'slug' => 'languages']
        ];

        $categories = [];
        foreach ($categoriesData as $catData) {
            $categories[] = Category::firstOrCreate(['slug' => $catData['slug']], $catData);
        }

        $coursesData = [
            [
                'title' => 'تطوير تطبيقات الويب باستخدام Laravel',
                'description' => 'تعلم كيفية بناء تطبيقات ويب حديثة وقوية باستخدام إطار العمل Laravel من الصفر وحتى الاحتراف. تشمل الدورة بناء نظام متكامل مع قواعد البيانات وتوثيق المستخدمين.',
                'category_index' => 0,
                'difficulty' => 'intermediate',
                'price' => 49.99,
                'sections' => [
                    'مقدمة في Laravel' => ['إعداد بيئة العمل', 'فهم بنية المجلدات', 'المسارات (Routing)'],
                    'قواعد البيانات' => ['الهجرات (Migrations)', 'النماذج (Models)', 'العلاقات (Relationships)'],
                    'الواجهات' => ['محرك Blade', 'إرسال البيانات للواجهة', 'مكونات الواجهة (Components)'],
                ]
            ],
            [
                'title' => 'أساسيات لغة بايثون للذكاء الاصطناعي',
                'description' => 'اكتشف عالم الذكاء الاصطناعي من خلال تعلم لغة بايثون. هذه الدورة مصممة للمبتدئين وستأخذك خطوة بخطوة لفهم المتغيرات، الدوال، والمكاتب الأساسية مثل Numpy و Pandas.',
                'category_index' => 0,
                'difficulty' => 'beginner',
                'price' => 29.99,
                'sections' => [
                    'مفاهيم بايثون الأساسية' => ['المتغيرات وأنواع البيانات', 'الجمل الشرطية والتكرار', 'الدوال (Functions)'],
                    'التعامل مع البيانات' => ['القوائم والقواميس', 'مكتبة Numpy', 'مكتبة Pandas'],
                    'مقدمة في الذكاء الاصطناعي' => ['مفهوم تعلم الآلة', 'أول نموذج لك', 'تقييم النموذج'],
                ]
            ],
            [
                'title' => 'احتراف تصميم واجهات المستخدم (UI/UX)',
                'description' => 'تعلم كيف تصمم واجهات مستخدم جميلة وعملية باستخدام Figma. سنغطي مبادئ التصميم، تجربة المستخدم، كيفية عمل النماذج الأولية (Prototyping)، وتسليم التصاميم للمطورين.',
                'category_index' => 1,
                'difficulty' => 'beginner',
                'price' => 39.00,
                'sections' => [
                    'مبادئ التصميم' => ['الألوان والتباين', 'الطباعة والنصوص', 'المساحات والمسافات'],
                    'استخدام Figma' => ['واجهة البرنامج', 'أدوات الرسم', 'مكونات التصميم (Components)'],
                    'تجربة المستخدم (UX)' => ['فهم احتياجات المستخدم', 'رسم مسار المستخدم', 'الاختبار والتقييم'],
                ]
            ],
            [
                'title' => 'التسويق الرقمي المتقدم',
                'description' => 'دليلك الشامل لاحتراف التسويق الرقمي. تعلم كيفية إدارة الحملات الإعلانية على منصات التواصل الاجتماعي، تحسين محركات البحث (SEO)، وتحليل أداء الحملات باستخدام Google Analytics.',
                'category_index' => 2,
                'difficulty' => 'intermediate',
                'price' => 59.99,
                'sections' => [
                    'أساسيات التسويق' => ['ما هو التسويق الرقمي؟', 'تحديد الجمهور المستهدف', 'بناء استراتيجية التسويق'],
                    'إعلانات منصات التواصل' => ['إعلانات فيسبوك وانستجرام', 'إعلانات جوجل (Google Ads)', 'إعلانات تيك توك'],
                    'تحليل البيانات' => ['إعداد Google Analytics', 'تتبع التحويلات', 'تحسين أداء الحملات'],
                ]
            ],
            [
                'title' => 'برمجة تطبيقات الأندرويد باستخدام Kotlin',
                'description' => 'اصنع تطبيقك الأول على نظام الأندرويد. سنتعلم لغة Kotlin الحديثة والموصى بها من جوجل، وسنبني تطبيقات حقيقية تتصل بالإنترنت وتعرض البيانات بتصميم جذاب.',
                'category_index' => 0,
                'difficulty' => 'intermediate',
                'price' => 45.00,
                'sections' => [
                    'أساسيات Kotlin' => ['المتغيرات والثوابت', 'التحكم في التدفق', 'البرمجة كائنية التوجه (OOP)'],
                    'واجهة المستخدم في الأندرويد' => ['تصميم الواجهات بـ XML', 'التعامل مع الأزرار والنصوص', 'القوائم (RecyclerView)'],
                    'الاتصال بالإنترنت' => ['استخدام مكتبة Retrofit', 'جلب البيانات (APIs)', 'عرض الصور'],
                ]
            ],
            [
                'title' => 'تحليل البيانات باستخدام Excel و PowerBI',
                'description' => 'اكتسب مهارات تحليل البيانات المتقدمة. تعلم الدوال المعقدة في Excel، الجداول المحورية (Pivot Tables)، وكيفية بناء لوحات معلومات تفاعلية ومبهرة باستخدام Power BI.',
                'category_index' => 2,
                'difficulty' => 'beginner',
                'price' => 25.00,
                'sections' => [
                    'إكسيل للمحللين' => ['الدوال المتقدمة', 'تنظيف البيانات', 'الجداول المحورية'],
                    'مقدمة في Power BI' => ['استيراد البيانات', 'تنسيق البيانات (Power Query)', 'إنشاء العلاقات'],
                    'لوحات المعلومات (Dashboards)' => ['إضافة الرسوم البيانية', 'تنسيق اللوحة', 'مشاركة التقرير'],
                ]
            ],
            [
                'title' => 'احتراف التصوير الفوتوغرافي',
                'description' => 'ارتقِ بمهاراتك في التصوير إلى المستوى التالي. افهم مثلث التعريض (الآيزو، فتحة العدسة، وسرعة الغالق)، وتعلم فنون التكوين، وتعديل الصور باستخدام Adobe Lightroom.',
                'category_index' => 1,
                'difficulty' => 'beginner',
                'price' => 0.00, // Free course
                'sections' => [
                    'أساسيات الكاميرا' => ['كيف تعمل الكاميرا؟', 'مثلث التعريض', 'أنواع العدسات'],
                    'فنون التكوين' => ['قاعدة الأثلاث', 'الخطوط القيادية', 'التأطير'],
                    'المعالجة الرقمية' => ['واجهة Lightroom', 'تعديل الألوان والإضاءة', 'تصدير الصور'],
                ]
            ],
            [
                'title' => 'اللغة الإنجليزية للأعمال',
                'description' => 'حسن مهاراتك في التواصل باللغة الإنجليزية في بيئة العمل. تعلم كيفية كتابة رسائل بريد إلكتروني احترافية، إدارة الاجتماعات، وتقديم العروض التقديمية بثقة.',
                'category_index' => 3,
                'difficulty' => 'intermediate',
                'price' => 19.99,
                'sections' => [
                    'المراسلات المهنية' => ['كتابة الإيميل', 'الردود الاحترافية', 'طلب الإجازات والموافقات'],
                    'الاجتماعات' => ['إدارة النقاش', 'التعبير عن الرأي', 'الاتفاق والاختلاف'],
                    'العروض التقديمية' => ['هيكلة العرض', 'لغة الجسد', 'التعامل مع الأسئلة'],
                ]
            ],
            [
                'title' => 'إدارة المشاريع الرشيقة Agile & Scrum',
                'description' => 'دليلك لفهم وإدارة المشاريع البرمجية بمرونة وفعالية. تعلم أدوار فريق السكروم، الاجتماعات اليومية، وتخطيط سباقات العمل (Sprints) لتحقيق أقصى إنتاجية.',
                'category_index' => 2,
                'difficulty' => 'expert',
                'price' => 75.00,
                'sections' => [
                    'مقدمة في Agile' => ['تاريخ Agile', 'القيم والمبادئ', 'الفرق بين Agile و Waterfall'],
                    'إطار عمل Scrum' => ['أدوار الفريق (Scrum Master, Product Owner)', 'أحداث السكروم', 'التحف (Artifacts)'],
                    'التطبيق العملي' => ['كتابة قصص المستخدم', 'تقدير المهام', 'متابعة التقدم (Burndown Chart)'],
                ]
            ],
            [
                'title' => 'أمن المعلومات والهاكر الأخلاقي',
                'description' => 'تعلم كيفية حماية الأنظمة واكتشاف الثغرات. تغطي الدورة أساسيات الشبكات، تقييم نقاط الضعف، والهندسة الاجتماعية للحماية من الاختراقات الحديثة.',
                'category_index' => 0,
                'difficulty' => 'expert',
                'price' => 99.99,
                'sections' => [
                    'أساسيات الشبكات والأمن' => ['مفاهيم الشبكات', 'أنواع الهجمات', 'التشفير الأساسي'],
                    'جمع المعلومات والمسح' => ['الاستطلاع (Reconnaissance)', 'مسح المنافذ', 'اكتشاف الثغرات'],
                    'الهندسة الاجتماعية' => ['ما هي الهندسة الاجتماعية؟', 'التصيد الاحتيالي (Phishing)', 'طرق الحماية والتوعية'],
                ]
            ],
        ];

        foreach ($coursesData as $courseInfo) {
            $course = Course::create([
                'instructor_id' => $instructor->id,
                'category_id' => $categories[$courseInfo['category_index']]->id,
                'title' => $courseInfo['title'],
                'slug' => Str::slug(Str::ascii($courseInfo['title'])) . '-' . uniqid(),
                'description' => $courseInfo['description'],
                'price' => $courseInfo['price'],
                'difficulty' => $courseInfo['difficulty'],
                'status' => 'published',
            ]);

            $sectionOrder = 1;
            foreach ($courseInfo['sections'] as $sectionTitle => $lessons) {
                $section = Section::create([
                    'course_id' => $course->id,
                    'title' => $sectionTitle,
                    'order' => $sectionOrder++,
                ]);

                $lessonOrder = 1;
                foreach ($lessons as $lessonTitle) {
                    Lesson::create([
                        'section_id' => $section->id,
                        'title' => $lessonTitle,
                        'content' => 'هذا هو المحتوى النصي لدرس ' . $lessonTitle . ' يمكنك مشاهدة الفيديو المرفق لمعرفة المزيد.',
                        'order' => $lessonOrder++,
                    ]);
                }
            }
        }
        
        $this->command->info('تم إنشاء 10 دورات بنجاح مع الوحدات والدروس!');
    }
}
