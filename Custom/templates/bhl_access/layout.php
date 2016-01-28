<div id="tabs_main">
    <ul>
        <li><a href="#tabs_main-1">Book Search</a></li>
        <li><a href="#tabs_main-2">ID Search</a></li>
        <li><a href="#tabs_main-3">Pick A Title</a></li>
    </ul>
    <div id="tabs_main-1">
        <?php print $ctrler->render_layout(@$params, 'booksearch-form') ?>
    </div>
    <div id="tabs_main-2">
        <?php 
        print $ctrler->render_layout(@$params, 'titlesearch-form');
        print $ctrler->render_layout(@$params, 'itemsearch-form');
        print $ctrler->render_layout(@$params, 'pagesearch-form');
        print $ctrler->render_layout(@$params, 'pagetaxasearch-form');
        ?>
    </div>
    <div id="tabs_main-3">
        <?php print $ctrler->render_layout(@$params, 'titlelist-form') ?>
    </div>
</div>

