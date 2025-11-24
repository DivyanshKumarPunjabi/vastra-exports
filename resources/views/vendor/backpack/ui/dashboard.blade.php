@extends(backpack_view('blank'))

@php
    use App\Models\BusinessUser;
    use App\Models\Advertisment;
    use App\Models\BusinessCategory;
    use App\Models\BusinessSubCategory;
    use App\Models\Business;
    use App\Models\BusinessRequestManagment;
    use App\Models\Location;
    use App\Models\Event;
    use App\Models\Address;
    use Backpack\CRUD\app\Library\Widget; 

    $businessUsers = BusinessUser::where('status',1)->count();
    $businessCategories = BusinessCategory::where('status',1)->count();
    $businessSubCategories = BusinessSubCategory::where('status',1)->count();
    $businessCounts = Business::where('status',1)->count();
    $businessAdvs = Advertisment::where('status',1)->count();
    $businessApplications = BusinessRequestManagment::where('is_business_approved', 0)->count();
    $eventsCount = Event::where('status',1)->count();
    $addressCount = Address::count();
    // notice we use Widget::add() to add widgets to a certain group
    Widget::add()->to('before_content')->type('div')->class('row mt-3')->content([
        // notice we use Widget::make() to add widgets as content (not in a group)
        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-primary text-white')
            ->statusBorder('start') // start|top|bottom
            ->accentColor('light') // primary|secondary|warning|danger|info
            ->ribbon(['top', 'la-user']) // ['top|right|bottom']
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('business-user'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessUsers)
            ->description('Business Users')
            ->progress(min(100, 100 * (int) $businessUsers / 1000)),

        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-success text-white')
            ->statusBorder('start')
            ->accentColor('light')
            ->ribbon(['top', 'la-briefcase'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('business'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessCounts)
            ->description('Active Business Listings')
            ->progress(min(100, 100 * (int) $businessCounts / 1000)),

        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-warning text-dark')
            ->statusBorder('start')
            ->accentColor('dark')
            ->ribbon(['top', 'la-th-list'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('business-category'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessCategories)
            ->description('Business Categories')
            ->progress(min(100, 100 * (int) $businessCategories / 1000)),

        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-warning text-dark')
            ->statusBorder('start')
            ->accentColor('dark')
            ->ribbon(['top', 'la-th-list'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('business-sub-category'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessSubCategories)
            ->description('Business Sub Categories')
            ->progress(min(100, 100 * (int) $businessSubCategories / 1000)),

        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-danger text-white')
            ->statusBorder('start')
            ->accentColor('light')
            ->ribbon(['top', 'la-bullhorn'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('advertisment'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessAdvs)
            ->description('Business Advertisements')
            ->progress(min(100, 100 * (int) $businessAdvs / 1000)),
        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-info text-white')
            ->statusBorder('start')
            ->accentColor('light')
            ->ribbon(['top', 'la-bullhorn'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('business-request-managment'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($businessApplications)
            ->description('Pending Business Applications')
            ->progress(min(100, 100 * (int) $businessApplications / 1000)),
        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-danger text-white')
            ->statusBorder('start')
            ->accentColor('light')
            ->ribbon(['top', 'la-bullhorn'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('event'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($eventsCount)
            ->description('Business Events')
            ->progress(min(50, 100 * (int) $eventsCount / 1000)),
        Widget::make()
            ->type('progress')
            ->class('card mb-3 bg-danger text-white')
            ->statusBorder('start')
            ->accentColor('light')
            ->ribbon(['top', 'la-bullhorn'])
            ->progressClass('progress-bar')
            ->wrapper([
                'element' => 'a',
                'href' => backpack_url('address'),
                'class' => 'col-sm-6 col-lg-3 text-decoration-none',
                'style' => 'display: block'
            ])
            ->wrapperClass('col-sm-6 col-lg-3')
            ->value($addressCount)
            ->description('Business Addresses')
            ->progress(min(50, 100 * (int) $eventsCount / 1000)),
    ]);

    $widgets['before_content'][] = [
        'type' => 'div',
        'class' => 'row',
        'content' => [ // widgets
            [
                'type' => 'chart',
                'wrapperClass' => 'col-md-6',
                // 'class' => 'col-md-6',
                'controller' => \App\Http\Controllers\Admin\Charts\RecentBusinessesChartController::class,
                'content' => [
                    'header' => 'New Businesses', // optional
                    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional

                ]
            ],
            [
                'type' => 'chart',
                'wrapperClass' => 'col-md-6',
                // 'class' => 'col-md-6',
                'controller' => \App\Http\Controllers\Admin\Charts\RecentBusinessUsersChartController::class,
                'content' => [
                    'header' => 'New Business Users', // optional
                    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
                ]
            ],
        ]
    ];

    $widgets['after_content'][] = [
        'type' => 'div',
        'class' => 'row mt-3',
        'content' => [ // widgets
            [
                'type' => 'chart',
                'wrapperClass' => 'col-md-6 mb-3',
                // 'class' => 'col-md-6',
                'controller' => \App\Http\Controllers\Admin\Charts\WeeklyUsersChartController::class,
                'content' => [
                    'header' => 'Business Categories', // optional
                    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
                ]
            ],
            [
                'type' => 'chart',
                'wrapperClass' => 'col-md-6',
                // 'class' => 'col-md-6',
                'controller' => \App\Http\Controllers\Admin\Charts\WeeklyBusinessChartController::class,
                'content' => [
                    'header' => 'Business Applications', // optional
                    // 'body' => 'This chart should make it obvious how many new users have signed up in the past 7 days.<br><br>', // optional
                ]
            ],
        ]
    ];


@endphp



@section('content')
@endsection
