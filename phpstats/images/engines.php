<?php
if(!isset($_GET)) $_GET=$HTTP_GET_VARS;
if(isset($_GET['q'])) $q=addslashes($_GET['q']); else $q='unknown';
header('Expires: '.date('D, d M Y',time()+31536000).' 00:00:00 GMT');
header('Content-Type: image/gif');
echo base64_decode(image("$q"));
exit;

function image($tag) {
$tmp='R0lGODlhEAAQA';
switch($tag){
        case 'Alexa': return $tmp.='LMNAFFRURoaGjU1NHBwb46OjgAAf8fHx6qqtQAAq1JS6wwM+f///wICAv///wAAAAAAACH5BAEAAA0ALAAAAAAQABAAAAR0sMl2kroqncmtQiByJVxjIUyqiqRkFUwwDASQFhn1MYIBBIIFISUyKVKExSF1MKgyO97BxmiuLiiVjGBw3rAq7iBg/UaTglQ3xmAdGQtv0CCwZQ67JGGWpOM2Lww0NgF7OC1GMCpfiC5YBQUiOSUUHiMbExEAOw==';
        case 'Alltheweb': return $tmp.='IAAAAAAhP///yH5BAAAAAAALAAAAAAQABAAAAIbjI+py+0PYwIUmGoDxrf2nXEaGHojJaXqyqoFADs=';
        case 'Altavista': return $tmp.='MQAACBKpNHb7QAgjnONxoWczQYxmAAajElmqUBksrHB4RpDoRM7nGaBveXp9Fp4uPT2+zdbrBVBn5aq1UtstcHN5vj6/Q06mxpAmyhSqOvv90trqwAikQApkwAxmP///wAAACH5BAAAAAAALAAAAAAQABAAAAWpoCeK2mVZlzau3tG93Ma9CIu9HadM2Mw5Iw0uB6lQFhtAJiPCxTadQAVimHgkHmEO4EBsBp6BATs5XGDYjALgSRQagcXCklMwPYwNpWGVbE4dGwxtGRQCYBQVE39nehkYCR4KGA8eAQUcKRuUDQBYAwKRBAIdKociGRWWHAATETMiBxRXBASpCAYcMyoiFQEcAgYEHgRQHRgsDDMdEQg3HQcsQRcFBSksIQA7';
        case 'Aol': return $tmp.='OYAAAAAlQAAkQAAmE5QuQAAlgAAkgAAk0dJttfc8g0NmYeIz9Xa8Hd9y+ru+RMTn4uP0kpMtxkbonJ0yJia1tTV7wICmh4epYKGz8nK6hobonByx09Turq75RUVoYiI0D09sUhOuSkqqQAAj01PuT4+shQUoYaHzuPj9FBTuHF4ynFxxycoqcrM7AQEl9vc8UpKtmFhwK6v38nN629vxW9xxyUmp2xuxWxxxpiY1enq9xcYoObm9UVGtebl9QAAlxIVoB4fpoeK0B0fpGBhwJKX1pKX1T4/smBgvzc4rwMDmkpKt1ZavbW54+br9/f3/IyN0VxcvrGz4T1As8TJ6ru+5n+Czb295fT2/BoapMnK6c/P7HZ2yIeH0AAAlAAAmf///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAeZgF+Cg4SFhoJRVoeHL0aLhRcEBgyPgiwJLVhAU5UwUhxfPRSPWQoIQRILlRo2JV0FP0xOV4YeISs+Xl4FKDEmhU0RHUoEugAbTw47hEQBUEddugRIH10Pgzk1BVxbIgICABlDAkINgjMGACouJEkVFikgAF03XxMBXQY6J19aGF9FAswLgIPGgQEjDlAZJGMJhAEDeFSpZCgQADs=';
        case 'Arianna': return $tmp.='KIAAJmZ/zMzzAAzzGZmzGaZzMzM/wAAzP///yH5BAAAAAAALAAAAAAQABAAAANHeLrcviWQR4sxhbZxs16X8RTCpIQKAIVeOIlnCIfCAR/cNcSXzQi9Q4AmNOwYOdfBYiBkCgRZ0Cat3qJWFgNr9TRIoYD3kAAAOw==';
        case 'Ask.com': return $tmp.='OYAAPfv7+/v7729vd45Of/355wAAN7e3oyMjN5jWrUAAM4ICO+1rWMICGNSUtbOzr0AAHtzc+eEhIwAAMbGxrW1taUAAN5KSu+9vUIhIe/W1r0YGGMhId5SUq2trbWtredzc//3995rY3MQEO+cnK2lnL0xMdZCOffe3tZSUueEe5Rzc969ta2lpaVSUoQQEK2UlGM5OUIpKVIAAN7e1jkYGM45OXNza95SSv/v3s4QEGMxMe+UlK1zc5QYEHsAAMaEhNYpKdbW1ud7e3MxKaWUlOfn53NjY3tCQr1SUu+tpd5aWvfOzr0QEJxKSuetpWNaWoxSUt6trZRCQtYxMVIQEIR7e3Nzc//v71oQELW1rVJCQloAAHtra9ZCQs4YGNYhIcYAAP//7/f3984AAP///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAfwgGGCggRhGUkpCCEAHiuFgwQkFl5gY2NeV1UJXYMLCEQPY5VjQGQNl2EEIQoRLAmjYBxkOmNfOF1jCidZFaKWH2JYUzU3lRZkAgVjHBFeOwZHYg1flSNiDgUIYgEwFxMeAhteYzkAHUESCwJUDCcTYloa1EoBNkU+CwElCmJiBjJfxl2gACWAiC5kMpQgM0NMjy8QzUUxOMaaCjIQQDTx4kUImY9ihnwBdkDMBgAvvHxZ4uAJBDFcAIgRcEAAgxYxNKAAYUUCCjJGSByg4WJLBTBgvjgxgAEMEwwMEhQIBQsJjwZSBnTRaoKr1i4/KAQCADs=';
        case 'Bismark.it': return $tmp.='OYAAB4sSzJGdCU2WlhxriIsQhwkNTRKfBMVFDtOfSw/aVdmkiEuSxUcKyIoOTA+YkFVhi5BbQoLDSEmMyk6YlZYXFVYWyw8YTM5SyMwTj5UiTlEYwECAjxCUxUeMYWMlmuBvSY4X36DixsmPikxRU9SVAcICyk2VSYtPx0oQgQFBw0PER0nPyIvUCEwUUxPUlxoj1lplo+Yo5adpTFCbDhLeCw9ZC9HeCs9ZSo8Yio7ZEplomuEwkRVgEJVgg4SHF94tnqQyVdxsi48Wig4WjxWjTM/XzY/VU1oqEZckxkjORMZKFx1tREWH4GKkyc2WSMyVCpCcyc6YsPR62Fzp3uBiB8vT3WFtc3Z7y1EdTBFcik4XCQxUCIkJVBklUJYi1RkkUBTgXmBn1Nrp6q63ywvMR4iLT5RfyQrPH2EjJWjzjJHeFFgi3N7mmd0n1ZtqGt8rKSz2qGz3EVQcaOw2DpRhjZRioiPl1xuoD9alhknRR4oP4OWyXF3fxciOGx+tgAAACH5BAAAAAAALAAAAAAQABAAAAfxgH+CghsRKhuDiSUlKSmLJRGMjpAqKnIKa18IDhgFBGZgJiYSZRJeH1JAdVEACy9LQTMQBEw+eQ93CCwtADh7c3BvdAIdShpFVQAiDAUWIA5fbhhKDExxVjA9GARdOghhbANDIkIFU2NxPwltO2oIaVcfBlk0NU5IDzkCPA8LKw5iMkwIgOeGHiwJUCSpQoxRFhtOtKgBkGAAjS0rWLBQkkKChgxOcEzoA+EIERBOBLToYGTPHj9QEjxBcWLEiDMNckq4wIHDhZonzgw60CjRnwNGVVCJ4UGGnRAh0PDx0ITM0AokKFBwQaJCha0UuAgKBAA7';
        case 'Dogpile.com': return $tmp.='OYAAOTl//7+/6bE//P2//L1/+3s/0VmhoKe/4KRw5209VRyupSY/5uk/0dnj5Gm/6qu/8DK/2WA/7jI//Py/7LA9Zq1/3+f6n6c/3CT9aG+/+Ti/2yEzVJxl6e6/6rB/+bv/6i3/05tqkpqmOHk/9nX/2d+r8fo/9jY/5Wv/3yL//X2/05wxLO//4qd06Kq67/O/+bp/7zJ/zxdfMPR8VR12rC3/5u8/2iEumN58tTl/9/q/XaG19XX8WOA1UFhg3ybzHqg/3iB9L7F/3WU/z9fho269VNyx6O2/6Sz8ev1/2uJwcfx/ri3/42n/8DE/4CYxkhmh7q4/4iW/8vd/0psw0pmkNPe/3qN0NHx//D//0Njh6jC+ZCr3K258lhrv1Z3qcfS/5qu/4+v8mmF5ktu0+nq/kZlmG+O9fH0/7/f8nGO3aux4U5tn1Bqod7f/5Gq4t3l/1V6mIWQzdrd/6XJ/m+Ax2OBx2qJ+abR/uru/3qMwEhljK7B/8fh/73h7kBggCH5BAAAAAAALAAAAAAQABAAAAfogH+Cg0Q/Lw80fw2Lg38icRwrSDAUVBwJW3eNFkt0X14pam03cHMXgjIyFi8VGy18ECwYdnVGgggICgoYTiRCEjkzCHorggUmO3I8MQwpETUEMSOnfwdADwAdESAjIA4fFSoZgmZBACclGyduUg4ACwMCggYJAyQlLZQ42PDyf1BdVARwgKfPFSVTrISJN08MmgAewEA4wOIDCiYEOgjScgZLABMZIGjQIGEBAAIMBPkIUSRJFgFNjrx5EiUAgSGN2LgoUAAFGQVrJkxIEKLRnypcdORJ46dAGRcijM7rccCGBwZj9gwKBAA7';
        case 'Excite': return $tmp.='MQAAMbGxs4pKTkYGFpjY5SUlISEhLW1tff396Wlpa0YGHt7eyEhId7e3v8QEAgICJwAAEpKSloAADkAAMwAAP////8AADMzMwAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAV64CVe0SRK0zOu4jNJl1QlYqCS0OVMEVlFDlnPB3sEJJJEzyg4BY6xIJAUgbUe02QjIoAsVkiLwiKrQAgK1qhgGGAVAIt6RGBAIG3HfA0AEJp7IhYAFAWBFxZ4BwcGcnMSBRQUCAMACFZqEIsQEggUA4ECjhAUBJiHpyEAOw==';
        case 'Google': return $tmp.='OYAAERx0ypnuRcvx9waAP/9+dEnKf/6/llutvb9//b/8hc5uik3s0i3UYiP1SYuku3//hll5NocEBUfgCtX1//5+FO2OxpZ9//2/+n//8smABVn0Rpf1vr/4dUjG//19dHm/x1Gskq1OhQrpfb7/iNcy02yQ/r7/+X2/vj///v///X/6//9///9/fn9//P///D//+7/9v/z/+f5//3/6f7/9Adj6KOpzb/e8LzX/9IoDxc7k//07QAilu7u/9/p/vj/5vD/93yPx83c82+s7//45v/42Ue5SsrS1MTb4/b66wATVsLO/tPZ+fz////w9sH1//7/9/Dw+Pz/9jdb1u//82KEqej/9la2Svv192+a6U+tVff/+/f/7eT29vL8/ffx//r89v7//6q2wholj3qk1mWF2MrT8oCc5Mrq+RxasS23QCkwdPr+7w0UosnX/+Hf7M4UGc4SC6/S+iJg2dfy/+r5///1+hw4jP/x+QQ2sSFY6AAlvf7/4//8/zy1RP///yH5BAAAAAAALAAAAAAQABAAAAf/gDVXIVolDBWGfhVGIWp+fhNJf1h8Dyknby5RLRcUeAUWKCgPdGcHCwJ5AUM3CUQZGlAwTwAKPhhNGG53az0xcSR8KGUOdXxsLC19QkcJFDkQI2c6TCg/QBwJFyZ9CTMZeihZChcENAYvLx4pNFwGcRMPAWkzBAYXfX12K1IqFBEQHpAAQcEJDS8XoBBIsWJGkQIbXADgAYbFlz8urCBB88HMtzkqyOwJAgNFHyhLFohY0ABMBwt/ZAhog2NLmB19THyQYIMPnA00OPhw0KaBDBpUHshRIoZPBA0vECA4cWCMCAEKBICo0uVPhykx+PwZS3bsiD4XPHR4xLatWz8DAgIBADs=';
        case 'HotBot': return $tmp.='NUAAHAAAK8aGeQFB4UAAfknJ/6pp64AAP5UVXBtbf7z9H99fP+ZmWRISf9mZjP/AN0BAf8AAP/d3vvt7GYAAGZmZr1kE+jn6cjIyKB6ef/MzJkzM/jCwaqko404OLyPPP7Qz8xmZv9mmdvOzczMzMyZmeCop5mZmdcnJ5lmANATEeAUFcwzM3EpKl0qKfkGBV9aWv38/FUCAlPcBUsAAPsYF8EGBswAAJkAAP8zM////wAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAa9wJxQWFPRaILacJmrEXBQHIEGUS5TuEMhkkhkFlTb0NmIDGGwxIbgMghpuAj6U1gUJJaSQFyjLdJ1DQdZIxcrNTcCNBk5BQceMjIVNCAcGAY3Li4SOQ04Mg4OMi4nCgqYmhIwBzSgogIBCgg3Nw8QGzAhLhWRKDYaCAwDAwYPDRYiBAIPNjUBLy8sAAA5zSQXHBoBAR3QDBMxQjcGARgmshTeMTPhQgO0HQjpDC0z7EzD0+D290xCAPv6DQkCADs=';
        case 'Il-Trovatore': return $tmp.='OYAAMXFxbe2usHAxGxsbO8AAPv69q2trWZmZrW1tebn5uHe4f77/ikoKYF9gf345RERESIiIiYiJBQTFb6/uwB7AOni5x0YIPPx8SoxLOzp7e7++fT99fD+6aSlpfyhp0pKSgCOAHFua3p7ejMzMxsbG77Ew+r55nzKgERFR/0OD2tpcD1IRunq7fna4P/w7fD49xIdE7O8tv757PikpmW1Z4qKitXZ2dbd3u4MEVJJUP6dpPXn8xW6FEZGRRAICOH+34eHh3qIhSsmIgcACLn4v/UUFHx4dZrem/ze3s/v0lxRWc/h2fkAAAyiD/52hOP53vm1taHooYt/iQMECxKzEj1COsX8yOv27/UDB/+Vl1ZaWQKcAFVVVT09PVhYWPKklHZ2dp/aoQAREZjYourr6u/v7///+AgICPf398zMzAAAAP///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAfsgGeCampnhIOEiWtlaGtrF46RkmtkJFxkA2oTChAjjWifazUIayFnAgYPJAgLqApAZRgHaCISCF6EH2VgEB9qCWpdZQ1nAKkSHWiZEQdkaj0XImoACQwjawWZHQXOKGgNamkVDEJmZgM+aZRqK1dGQwDjGGtoB1MCa8BVZSFqJQoRxAhYoMLfmgwWlOyQYgFAGS1ncrAIAmOCowBpFtgIkGHNjQABXiyJQcZRmChHxiQxZwaJhywedLRYY2ULDypNTmxw9AVHiiJYoKwhQgEECAo0dq5xQoAAEwIzHD3hwOGHCWwaNLiQ4cCBuUAAOw==';
        case 'ixquick': return $tmp.='NUAAABLmAB72QBzzQBuxwBfswBVpQB20d/o8u/1+wBXqABSoQBpwYqsz4q741Wc1yF7xQCG5QBmuwBbrWaGsHaTuACD4jFloe/y9wBDjSmN2oq23QB1zwB92oqx1qq/2Iq95YqkxBFVnRFDhYqoyna565rA4bvF1oq/6d/l7hFbpM/k9orH8zFxr3aHqV6Mu3bB9c/b6arB2Xan0wBgs6q50ABoviFam7vU6orD7gAtcQA7hKq3zTF9wZrE5v///wAAACH5BAAAAAAALAAAAAAQABAAAAaBQJ9w6JMhiEjko5RsqgaOZpJkGBylw0xA0EtqGifc6gXhbDKaDmMEot0egkAFUtkuCAUAhnLxIRwGARwBVRESCiEeSB8CGwIDNTMJLAdNDQsLEQQJClg8BAQSnAAxTQcFqC4pehNNDAAADD4HFjoiTRY2MEQUOTtIKBN9SCYtWMdBADs=';
        case 'Jumpy': return $tmp.='OYAAAhjgv/4Ov//9htihQZgf//uOv/kJwJgf1eQpf/87tDf5P/3xP/kMkl5Z7bN1sXY43yVas/X3wBCaR5ri5a0gkCEnHiouwBNff/2l//wAP/hFH2rmKG/zy9naVSJqfP37Iesnf/gAtPPMFiWsv/lGvToOf/9ev/0tSt4keDbS+jmQv/jI//5wSp2mQtKcCRneUWGpKTE0Th/e+XhMv//mTJ2lDNrZSdyjP//KEB7nWOYrzdukmOHZeHry+vgMqzL3pm+y/zwoP/vl///MwA8boezxwAxX3qssHqkw//hGwA4X7C8k9Xg5IS1vcXb4f3rcf//Zvn6+///zO72+frmXtvj5zx/msjFKgBScwBbgv/nQ0+KnwBbe0yIngBKbQ9lg////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAfGgFuCXYSFhoJbXIpYXo2OjViKXF9fXhFgmJmYQAAAlAcoDlVMl5pbWJQAXFgSEkRGmk43B59WHghHGw6aSBedXEU/PRgmAQKZAksNWTUVHBQiBgZamgsBJRA7TTYzBVpJJ5pBKwUFLxM8Q1paGgmZCU8MWjgyWR0qAQxUx5gL0wE+vDzI0SBFBhaahIQgcQWEAgUTsriA8OEdFBpSBDzQMUCJFxgKNFlAMKLFgAFcCICJogkMAi9ZYhIgQEnTlBhYUFHaSSkQADs=';
        case 'Kataweb': return $tmp.='NUAAL0AEOdKUsYIGNYQGM4IGPfn560AELUAEP//76UAEP//9/fnvZwAEO/enOfetfetrd45QrUYKc5rMefOvf/nlL21jOecKfeUlL1KIYwpMdZ7UoQIENYACIQQIf/v1vfGxvfGUuecpb05Sv/Ozt4pMYQAEN6cnPe1td61Ws6thM4ACIwAEN4AEO9SWs7Ge3sQGNa9c//e1u/v7/eUnPetnM4xKbVjIefeve97e/f398YAEP/3984AENYAEP///wAAACH5BAAAAAAALAAAAAAQABAAAAaKQJ9wSCwahblJbncU7m4+WSpXESqIuwWi0XDAHK4dIncdelAajMRmAVEWRtMmckgcBIIarbjLlBgGAgAEPDokOUQFLysGADqPPDwDMYkdDAeOkZEDBUxOIgmOPaORAYhEIQaPPTysPCdGOxA6raMcLadFHwQqoz0DI007FwSjLDhNQw8BATO5yclBADs=';
        case 'Lycos': return $tmp.='KIAAHd3d6qqqlVVVSIiIgAzmczMzAAAAP///yH5BAAAAAAALAAAAAAQABAAAANPGHBRezDGMkAbRepDTTHPFgVGKYiSQKxEhh7qir1fab/BMNgeWggC3uC145VCkqLRENjsALtgaSipQUg8TY5au6UGJwiA5/rphj/BeT1IAAA7';
        case 'Metacrawler': return $tmp.='MQAAP/394yMjOfn58bGxr29vbW1tc7OztbW1oyEhDk5OZycnNZKSueEhMZCQud7e2tra//n50JCQjExMVpaWlJSUvf39////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAVEoCWOZGmewLIw51gRQ+NU7VEMFQC1hmIALZEAUaAFLYWHgGRkBiaVZisQWR4tgwTiKlJIAlyLgJII/K6VA4EQbrvflhAAOw==';
        case 'MSN': return $tmp.='OYAAPeljPf39+9zOe+EQvfGKfe9MRCMzgClUhCcc/ecSu97Oe9rMffv1iGUxmO9Y73e92u1563W5//WvXPGa+/GMQiEzkql3gCExsbOzmOtzu+MQv/371JztVKta/fGtSmU1giMvffereecWu/3/ymcc++9QgiMzsaUczmc1kJ7tefOe2tzrbXetXvG5/f/9ymEvf/v3hh7te+te3utxkJ7nK3exveUWkqtc5TetYx7lHPGc3N7nAice++tKZS1zlqt3t6UWt7v94zG5/+9KSmMWsbnxu/GvffOawiUnEqErf/OtffGIRicYyG1UhiMjKWEhPetlFprlAiUjACtUv/n1u/v7++MSu9jMe9aKf///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAe0gFmCg4RZAUpQHgGFhRI2WJBGERkzVY0DV5BYAC0GFy8+lllUVgKZkABBFgYmF0kYGDIDpliZAFlCKA0VFTFRQFazp7cjEBYfBiApTwlWVgpXV7dZDxk/DVJOKyLNAwtXotQQSAgIHCcazwILi4MRPAcHNDkJGgoCPRuEVTdTByQ7KBQYQmBJOEFFJuhwgIACgYdHXDBiMcEBExUFCpRgwChLlQ5NiASAEYJjR0M4apxcWSgQADs=';
        case 'Netscape-Search': return $tmp.='NUAAJycnAhraxiEhBB7eymUlBiMjMbW1kqcnAh7eyGMjAhjY2u1tSlja3u1tYyUlEqlpa3OzkKcnJSUlAiEhNbW1hCEjCmMjAhze63GxqXGxmutrUJKSoSUlFpaWiGEhABSUozGxnucnNbn50KMjKWtrYSMjAhrc1qMjABKSnOtrZylpYS9vYStrTmUlBAQEGNjY2OtrWNra9be5zGUlGtra6XOznuUlJS9vRCEhBiEjAhzcwAAAP///wAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAabQJ5QCMCkPIeVbcjkiWqNhyVXmCyavAwEtIgkCrkKrsW8XXmHWWIyRGiEHELkIETkcgNeQKcL8VgJBARDAgMXPHwmDTwPVDk8AjwGfIh8Izw4mTiYeZN6AQwKPAQCAhU8AzofknoKCgw8MAgWEEMUTCQoJzwyQjQ8O8HCwrcbwUPDwi5DAMnODkzNzsESWA4vyR0lWEMxACrVTUEAOw==';
        case 'PagineGialle': return $tmp.='NUAAP7iUxgIAXZVAKinpf774favQP+ZAP/lBcuOAPbVFr/G2P//mVBVZsWwT2ZmM//89Xd7hbGSSNvf6IaMpplmAD9HV0UqE5mZZszMzP//+P/MM/7//GZmZmYzM3+FjMyZZu7v9GZmAC8yRZuEAP/MZsnO2f/kLjMzAP//M8yZAAAzM+fWocxmAJlmZtq9KP//zAAAM8zMM/fWLvSUD/vVAP700fzphPzjb/WwAP/+//bFAfzzuAAAAP///wAAAAAAACH5BAAAAAAALAAAAAAQABAAAAa4wJ5QKFEodoShUjgJCBAIiiBiSyphggNNp8MZWIPbbsigHGSJrU5TmGEAtR6Ih7rZbo9abt+rEWQ9ECNiO3FCOT17OWIVLjY7D0olEAwSOTs2IjI2NRlDOQI6BgFHNwwpNwsrFy0cEw44MwUVLzcDITIkdgAHXbEfHgs2OTw0NAlpXLEFFgNwPQPFajixMxEqO4BCDjwIvgYzHTzZVkIxATwnFugX2YZKCwANDTY2JgAbS0o1O/36QQA7';
        case 'ScaricaGratis.com': return $tmp.='OYAANbW1s7O56213nOctb29vYSlxpy1xs7O72uctdbW3lqUrc7O1rW177291mOUtefOWr3O1oStvaWce4ytxt7e71KErffWUpS1xrW1rbW9587GjJStzr29tdbGe721nMa9hMbG1q3GzqWchLW1vaWlpdbOjM7e3nOcvdbe3t7GY62158a1a62ttb2te5ychM7Gvb29raWljIylxoSlvcbG56293oytvcbO1oSEhEqEpXulvcbGxs7Ozv/eQt7e3tbW597e5+fn5wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAfNgAA8BIQ7PD5BQIlBjEE8HB0PFg8vAECXmIk7Gj0SLh8wCwk/Pwc/FIo7Hz0PKyIsIAECCA4OEz9AOyQtKRY9Hg01OToqAgW4mzE4Er8NMjkMGQIZuDwlPdgdIw0F0AUVCgdAADsEGBgcIAsCORsMBQ4BQD6CPDwLAD8BGxU5OSe4gvgYODAREH00AohT1KjhJQq4LjWcyEgRw4YQdBgIoWNGEAMRdNiY0HCACSADEgy4EQECggQKGG28oOOCAR0IfLQcgCJmQ0QCKTIKBAA7';
        case 'Search-the-Web': return $tmp.='NUAACWq/wB6uQC5egBzANzcALm5AFZWVo7U/yVzAABKc6qqqlX/AKr/JZ6ennp6enq5AJKSkmJiYmvG/4//a3NzALj/SFBQAAD/VY6r/yYmJki4/47/jgCWMT4+Pqr/AJLcAABilkpKSgCq/zIyMgCS3P///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAaowJJQmBkZjcNkKZMhkThQEjKZAYk0nM+HQBgkRsNMNpLdXDyFjzdE3H4Kng8E8vA8QOzMx7PxeAQcGiQCHwYgBiEjHBt9HgQCIiQODgEgCYkge3wFGpFOJCB4I4MMGxwKIqmfoYkiAnwNBxoAnqCiJBoVGwoaBwCflQYdJSMAEwyxtJ8IA8PEARoYABKqJAgLFM4lIQGp1dcW2kIhIZ8kAc3iSeQd7epBADs=';
        case 'Search.ch': return $tmp.='JEAAP8AAAAAAP///wAAACH5BAAAAAAALAAAAAAQABAAAAIyjIWpxqEvmoOvQQAWuznZh2mHEJJYZ52ZmqWrCadKGLokXG8q3m4wH/FRPJ+hZGI0FAAAOw==';
        case 'Supereva': return $tmp.='MQAAKW9MYylY3OcUpurOq3Fc2+GlCExQnuUUqq6dXCEPyk6OrW9MSE6QoSWoUJTQmN7jHuMnMTOY5yqozpaa7XFOgAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAVRICWOZDSR6BhJk5OmrDG8JGAqQBoRhYRQk0kgEiCQgkgJC3kiCZhMSg4FDY6mo0d1ppM8mYGXtzqZhSmNAoQcBbKZCdHhjbx+gwzobDFq10chADs=';
        case 'Teoma': return $tmp.='NUnAFF3xGia/pSSjP5oBKhFA7PM/lJRTprMBGiKA9Xl9ai74QkJCNOigREQECIiIf6zgR4dHBwcGwYGBh8eHQQDA7PEgVFQTQwMDMzlgRcXFiEgH11bWAICAhIRER0dHOHd1eXy//b7/7azrMfc8gAAAFt1j////wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAACcALAAAAAAQABAAAAaBwJJwSBSejqfSaMlsjkpIJWgaMlmvz6MUFKpesVClqPv9ip5KEmfh0HQaE0+GREKPDAKR/iLRiwQGdl8fCyQfX4JXHxSGiEIjg3SHViGJJgIbEBEWAiYhIJZlniAJaBgHByYFAawBBSADAw9CFQgIJgoAugAKCQQEDI9Ow0tFxsZBADs=';
        case 'Tiscali': return $tmp.='NUAAO7n8cqv1eXb6enk7trM4evg78i119rJ5dO93vHu9fDu8q2HwL6XzNjL4Z5staV9u7CLw6N6ufDo9PXx9uPZ6eLa6bKbxc++2YdLoZBZq72kzqJ2uKFutrGHxJxxtKN2up91uuXZ6+fd7uDX58au1MSqz8KkzrWUxdC827ONxfb29/v6+72ezMy11t7O5tXF3v///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAbYQECqRXy5UCeWYfmyCCYcSCkQME1NLFbHIFnBvi+KV/RSfTUtL0xVcpxQrUXq0iqRNo9v6gSooEgjAy8tJCoKIGYOIkcQZQMkKRQuK5QrCisMCAMJLyQHIgkpAWowcS8wIxAWAzANJg8nXywnLg0vJkYNCCUALh5fDiMjKAsohiULLhIwIl8hpUYTIgTUKiYuX18uERcwACcnBTAVJx8IXwQpLyMARSMiJAkIGV8YKLcRLAUFLA8oJgcOfBGwRkUBBS8SCMAmQE2nAy4CMGCQJUWKLCVYjAgCADs=';
        case 'Virgilio': return $tmp.='KIAAMDAwICAgGBgYP8AAP//AAAAAP///wAAACH5BAAAAAAALAAAAAAQABAAAAM9aKq1vqCxJZ+oLIDHu/+MFBUkVQYUMRBSsQ6YqzbyGqtszSoB/eqVFu614+hsnsKQtTE0KUSMkVRAga6GBAA7';
        case 'Voil�.Fr': return $tmp.='LMAAP/qwOWUX/9VAP/Vf+np6f//v//Vv/+qf/T09P+qP/+/n/9/P/9/AP/VAP+qAP///yH5BAAAAAAALAAAAAAQABAAAARg8MlJq70464kAVYNCIYdjOonINI7xIIbTsCY7LKyYzM2CwDKHQpZisRISgowBkC2Wq0YiEVw8VkGFglEzLTw71velOAQOLsmBwVgwBAt0QYNQJN6CduKcARjMC250GxoRADs=';
        case 'Web.de': return $tmp.='LMAAGZmZpmZAP/MM8yZABYWFplmADMzAP/MAP///wAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAAAAAAALAAAAAAQABAAAAQ3EMlJq70Ync0xP8Z3UNwQGKhRHMImbYVBpOjqfmEOiviu8xvdrxOkGV0aIAeAfClHF15mSq1SIwA7';
        case 'Yahoo': return $tmp.='JEAAAAAAP8AAP///wAAACH5BAAAAAAALAAAAAAQABAAAAIolI+py+0dogwOyDMnjhuIu1GR9wVkaBrS2bXKiLBqVi7AjRvyw/dOAQA7';
        case 'unknown':
        default: return $tmp.='OYAANfV1nSDq2x1jZ2kt5+ltrG2xAAiehUydx0+jShFijZTmjlWnThUl0BdokFbnERenUxmpYeUtJGcuZ+ovpujuJScsKiuvRI0gxM1gxEvdRQzeBUzeBk3eSdFiyhGiiZBfi1MkytIjC5MkzFPlDdWnz9epHOBonSDo3iFo4SSsq2zwb7Cy7a/0K20wYmRlf//t//87P/876qjj9XSyuXi2vHu5vXz7vvGU66pnuvn3vDs4+jl3tjVzvr38PXy6/Dt5uvo4eDc1LWyrNHOyMvIwri2sunn4+28Z/LBbP/RgN/XyKWflPbu3+fj3MJ8CqBuG655H615ILJ9IrF9JaV1JLiEK7F+KbSBK/nEafTBbf3JdPvIc/7Md/rPhujChv747sS/t5KPirGuqfTx7PTw6vDs5u/r5e3p4+vn4Z+dmqOhnt3Y0ZOQjOXh3Pfz7tDMyJ2cm728u3dpXNzY1dDMyf///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAHUALAAAAAAQABAAAAeZgHWCHgkJHYKIiXUhFwVzADQnGh+KghgtTTpkbjkmGZUiKmtBaGZjPW0oB4ojdGA8Z2U/NjEDG4oMcWpEPz41Y18UHIoOM3BpQztARjATBooPEUVhbGJvTCwBLpUKKUJLOEorAjIvlRAkEgQVFiVyN15JlXUNCyAIgk5TR1zzlU9XkGjxpwiKlSxbCCaKQgVLF4WIpFSBWCkQADs=';
}
return $tmp;
}

?>