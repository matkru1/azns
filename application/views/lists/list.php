<div class="container">
    <div class="left">
        <?php 
            foreach ($playlist as $r) {
                echo '<div class="entry">';
                echo '<div class="title">';
                echo $r->author . ' ' . $r->title;
                echo '</div>';
                echo '</div>';
            }
        ?>  
    </div>
    <div class="right">
        <ul>
        <?php
            foreach ($stations as $k => $v) {
                echo '<li><a href="'.$v['url'].'">'.$v['name'].'</a></li>';   
            }
        ?>
        </ul>
    </div>
    
</div>