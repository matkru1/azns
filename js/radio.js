/**
 * @author Maciek
 */

function getPlaylist() {
	var id = $('#radioId').val(), baseUrl = $('#baseUrl').val(), url = baseUrl + "index.php/lists/xajax_getPlaylist/" + id;
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
		if (list[i].coverUrl != null) {
			html += '<img src="' + list[i].coverUrl + '" alt="' + list[i].author + '"/>';
		}
		html += '</div>';
		html += '<div class="entryData">';
		html += '<div class="title">';
		html += list[i].stime + ' <span class="author">' + list[i].author + '</span> ' + list[i].title;
		html += '</div>';
		html += '</div>';
		html += '<div class="clear"></div>';
		html += '</div>';
	}
	$('#playlist').html('');
	$('#playlist').html(html);
}

function getStats() {
	var baseUrl = $('#baseUrl').val(), url = baseUrl + "index.php/lists/xajax_getStats";
	$.ajax({
		url : url,
		dataType : 'json',
		type : "GET",
		success : function(response) {
			reloadStats(response);
		}
	});
}

function reloadStats(list) {
	var html = '';
	var autors = list.autors;
	html += '<ul>';
	for (var i = 0; i < autors.length; i++) {
		html += '<li>' + autors[i].autor +  ' grał: ' + autors[i].liczba + '</li>';
	}
	html += '</ul>';
	$('#stats .autors .list').html('');
	$('#stats .autors .list').html(html);
	
	var titles = list.titles;
	html += '<ul>';
	for (var i = 0; i < titles.length; i++) {
		html += '<li>' + titles[i].autor + ' - ' + titles[i].tytul +  ' grał: ' + titles[i].liczba + '</li>';
	}
	html += '</ul>';
	$('#stats .titles .list').html('');
	$('#stats .titles .list').html(html);
}


$(document).ready(function() {
	setInterval(getPlaylist, 30000);
	setInterval(getStats, 30000);

});
