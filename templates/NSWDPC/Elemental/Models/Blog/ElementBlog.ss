<div class="{$ElementStyles}">
    <div class="blog-element__content">
	    <% if $ShowTitle %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
	   $HTML
        <ul class="blog-list blog-post-list">
            <% loop $RecentPosts %>
                <li class="blog-post $FirstLast">
                <div class="blog-post-body">
                    <h4 class="blog-post-heading"><a href="$Link" title="More information about $Title">$MenuTitle.XML</a></h4>
                    <p class="small">$Date.Full</p>
                </div>
            </li>
            <% end_loop %>
        </ul>
        <p class="more">
            <a title="View more news" href="$Blog.Link">
                <% if $BlogLinkTitle %>{$BlogLinkTitle}<% else %>View more news<% end_if %> <span aria-hidden="true" class="fa fa-chevron-right"></span>
            </a>
        </p>
    </div>
</div>
