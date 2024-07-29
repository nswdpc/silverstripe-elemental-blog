<% if $Blog %>
<div class="{$ElementStyles}">
    <div class="blog-element__content">
	    <% if $ShowTitle && $Title %>
            <h2 class="content-element__title">{$Title.XML}</h2>
        <% end_if %>
        <% if $HTML %>
	       {$HTML}
        <% end_if %>
        <% if $RecentPosts %>
            <ul class="blog-list blog-post-list">
                <% loop $RecentPosts %>
                    <li class="blog-post $FirstLast">
                        <div class="blog-post-body">
                            <h4 class="blog-post-heading"><a href="{$Link}">{$MenuTitle}</a></h4>
                            <p class="small">{$Date.Full}</p>
                        </div>
                    </li>
                <% end_loop %>
            </ul>
            <p class="more">
                <a href="{$Blog.Link}"><% if $BlogLinkTitle %>{$BlogLinkTitle}<% else %><%t NSWDPC\Elemental\Models\Blog.VIEW_MORE 'View more news' %><% end_if %></a>
            </p>
        <% else %>
            <p class="no-posts">
                <%t NSWDPC\Elemental\Models\Blog.NO_POSTS_TO_SHOW 'No posts' %>
            </p>
        <% end_if %>
    </div>
</div>
<% end_if %>
