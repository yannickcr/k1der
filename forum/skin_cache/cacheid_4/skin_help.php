<?php
/*--------------------------------------------------*/
/* FILE GENERATED BY INVISION POWER BOARD           */
/* CACHE FILE: Skin set id: 4                     */
/* CACHE FILE: Generated: Wed, 31 Aug 2005 00:22:47 GMT */
/* DO NOT EDIT DIRECTLY - THE CHANGES WILL NOT BE   */
/* WRITTEN TO THE DATABASE AUTOMATICALLY            */
/*--------------------------------------------------*/

class skin_help {

//===========================================================================
// display
//===========================================================================
function display($text="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
          <!-- Displaying Help Topic -->
          <tr>
            <td class='row1' colspan='2' class='postcolor'>$text</td>
          </tr>
          <!-- End Display -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// end
//===========================================================================
function end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
          </table>
          </td>
          </tr>
          </table>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// help_end
//===========================================================================
function help_end() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
<tr>
			<td class="catend"></td>
		</tr>
	</table>
</div>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// no_results
//===========================================================================
function no_results() {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
                <tr>
                   <td class='row1' colspan='2'><b>{$ibforums->lang['no_results']}</b></td>
                 </tr>
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// row
//===========================================================================
function row($entry="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
          <!-- Help Entry ID:{$entry[ID]} -->
          <tr>
            <td class='{$entry['CELL_COLOUR']}' style='height:28px'><a href='{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Help&s={$ibforums->session_id}&CODE=01&HID={$entry['id']}'><b>{$entry['title']}</b></a><br>{$entry['description']}</td>
          </tr>
          <!-- End Help Entry -->
EOF;

//--endhtml--//
return $IPBHTML;
}

//===========================================================================
// start
//===========================================================================
function start($one_text="",$two_text="",$three_text="") {
global $ibforums;
$IPBHTML = "";
//--starthtml--//


$IPBHTML .= <<<EOF
     <table cellpadding=4 cellspacing='0' border='0' width='<{tbl_width}>' align='center'>
      <tr><td>$two_text</td></tr>
     </table>
     <table cellpadding=0 cellspacing='1' border='0' width='<{tbl_width}>' bgcolor='<{tbl_border}>' align='center'>
      <tr>
        <td>
          <table cellpadding='4' cellspacing='1' border='0' width='100%'>
          <tr>
          <td  align='left' colspan='2' class='titlemedium' >$one_text</td>
          </tr>
          <tr>
              <td class='row1' colspan='2'>
               <form action="{$ibforums->vars['board_url']}/index.{$ibforums->vars['php_ext']}?act=Help;s={$ibforums->session_id};CODE=02" method="post">
               <input type='hidden' name='act' value='Help'>
               <input type='hidden' name='CODE' value='02'>
               <input type='hidden' name='s' value='{$ibforums->session_id}'>
               {$ibforums->lang['search_txt']}&nbsp;&nbsp;<input type='text' maxlength='60' size='30' class='forminput' name='search_q'>&nbsp;<input type='submit' value='{$ibforums->lang['submit']}' class='forminput'>
              </form>
             </td>
           </tr>
           </table>
          </td>
         </tr>
      </table>
      <br>
     <table cellpadding=0 cellspacing='1' border='0' width='<{tbl_width}>' bgcolor='<{tbl_border}>' align='center'>
      <tr>
        <td>
          <table cellpadding='4' cellspacing='1' border='0' width='100%'>
           <tr>
             <td colspan='2' class='titlemedium' ><b>$three_text</b></td>
           </tr>
EOF;

//--endhtml--//
return $IPBHTML;
}



}

/*--------------------------------------------------*/
/*<changed bits>
display,end,no_results,row,start
</changed bits>*/
/* END OF FILE                                      */
/*--------------------------------------------------*/

?>