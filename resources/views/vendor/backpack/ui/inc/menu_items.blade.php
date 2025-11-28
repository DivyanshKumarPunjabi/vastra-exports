{{-- This file is used for menu items by any Backpack v6 theme --}}
<li class="nav-item"><a class="nav-link" href="{{ backpack_url('dashboard') }}"><i class="la la-home nav-icon"></i> {{ trans('backpack::base.dashboard') }}</a></li>

<x-backpack::menu-item title="Blogs" icon="la la-blog" :link="backpack_url('blog')" />
<x-backpack::menu-item title="Enquiries" icon="la la-envelope" :link="backpack_url('enquiry')" />