function WPDownloadOpenDialog()
{
	var dlg = window.open("../wp-content/plugins/wp-download/tinymce/manager.php", "WPDownloadManager",
			      "toolbar=no,menubar=no,personalbar=no,width=500,height=210,scrollbars=no,resizable=yes,modal=yes,dependable=yes");
};

function WPDownloadInsertCode(URL, title, conditions, top10)
{
	var code = '[download title="'+title+'" DisplayConditionsOfUse="'+conditions+'" DisplayTop10="'+top10+'"]'+URL+'[/download]';
	buttonsnap_settext(code);
}
