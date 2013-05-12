    <input type="hidden" name="radioId" id="radioId" value="<?php echo $radioId ?>" />
    <input type="hidden" name="baseUrl" id="baseUrl" value="<?php echo $baseUrl ?>" />
	<div class="left">
		<embed type="application/x-shockwave-flash"
		flashvars="audioUrl=http://files.kusmierz.be/rmf/<?php echo $nameId ?>.mp3"
		src="http://www.google.com/reader/ui/3523697345-audio-player.swf"
		width="400" height="27" quality="best"></embed>
		<div id="playlist">
		<?php
            foreach ($playlist as $r) {
                if ($r->order == 0) {
                    $class = "entry current";
                } else {
                    $class = "entry";
                }
                echo '<div class="'.$class.'">';
                echo '<div class="cover">';
                if (!empty($r->coverUrl)) {
                    echo '<img src="' . $r->coverUrl . '" alt="'.$r->author.'"/>';
                }
                echo '</div>';
                echo '<div class="entryData">';
                echo '<div class="title">';
                echo $r->stime . ' <span class="author">' . $r->author . '</span> ' . $r->title;
                echo '</div>';
                echo '</div>';
                echo '<div class="clear"></div>';
                echo '</div>';
            }
		?>
		</div>
		</div>
		<div class="right">
		<ul>
		<?php
        foreach ($stations as $k => $v) {
            echo '<li><a href="' . $v['url'] . '">' . $v['name'] . '</a></li>';
        }
		?>
		</ul>
	</div>
	<script type="text/javascript" src="<?php echo $baseUrl ?>js/radio.js"></script>