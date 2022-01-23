  <form id="search" method="get" action="<?php bloginfo('url'); ?>/">
   <h2><label for="s">Rechercher :</label></h2>
   <p>
    <input type="text" id="s" name="s" value="<?php the_search_query(); ?>" />
    <input class="submit" type="submit" value="OK" />
   </p>
  </form>