<form method="GET" action="<?php echo get_bloginfo('url'); ?>" class="relative">
    <input type="text" name="s" class="border border-dark/10 px-4 py-2 text-sm rounded-full" value="<?php echo get_search_query(); ?>" placeholder="<?php _e('Search'); ?>">
    <button type="submit" class="absolute right-2 top-2">
        <i class="fa-solid fa-magnifying-glass text-dark/70" style="font-size:1.1rem;" aria-hidden="true"></i>
    </button>
</form>
