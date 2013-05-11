/**
 * @author Maciek
 */

function getPlaylist() {
	var id = $('#radioId').val(), baseUrl = $('#baseUrl').val(), url = baseUrl + "index.php/lists/xajax_getPlaylist/"+id;
	$.ajax({
		url : url,
		dataType : 'json',
		type : "GET",
		success : function(response) {
			reloadPlaylist(response);
		}
	});
}

function reloadPlaylist(list) {
	$('#playlist').html('');
	var html = '';
	for (var i = 0; i < list.length; i++) {
		var classs = '';
		if (list[i].order == 0) {
			classs = "entry current";
		} else {
			classs = "entry";
		}
		html += '<div class="' + classs + '">';
		html += '<div class="cover">';
		if (list[i].coverUrl != '') {
			html += '<img src="' + list[i].coverUrl + '" alt="' + list[i].author + '"/>';
		}
		html += '</div>';
		html += '<div class="entryData">';
		html += '<div class="title">';
		html += list[i].start + ' <span class="author">' + list[i].author + '</span> '+list[i].title;
		html += '</div>';
		html += '</div>';
		html += '<div class="clear"></div>';
		html += '</div>';
	}
	$('#playlist').html(html);
}


$(document).ready(function() {
	setInterval(getPlaylist,5000);
	
});
