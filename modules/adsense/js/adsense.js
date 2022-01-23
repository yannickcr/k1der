function adsenseStart() {
	domEl('iframe','',{name:'google_ads_frame',src:'http://pagead2.googlesyndication.com/pagead/ads?client=ca-pub-3073868596887719&dt=1151955929265&lmt=1151955911&format=160x600_as&output=html&channel=0298513714&url=http%3A%2F%2Fwww.k1der.net&color_bg=CC0000&color_text=000000&color_link=FFFFFF&color_url=FFFFFF&color_border=CC0000&ad_type=text&u_h=1024&u_w=1280&u_ah=949&u_aw=1280&u_cd=32&u_tz=120&u_his=1&u_java=true&u_nplug=27&u_nmime=112',allowtransparency:'true',frameborder:0,scrolling:'no'},$('adsense'));
}

addToStart(adsenseStart);