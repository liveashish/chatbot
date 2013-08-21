<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title></title>
    <link rel="stylesheet" href="style.css" media="screen" type="text/css" />
  </head>
  <body>
    <fieldset>
      <form name="changebot" action="?page=select_bots" method="post">
        <div>
          <label>Change Active Bot</label>
          <select name="bot_id" id="bot_id" onchange="submit()">
            <option value="new" selected="SELECTED">Add New Bot</option>
            <option value="1" selected="SELECTED">testBot</option>
          </select>
          <input id="action" name="action" value="change" type="hidden">
        </div>
      </form>
    </fieldset>
    <fieldset>
      <form name="botAttributes" action="?page=select_bots" method="post">
        <div class="leftHalf noBorder">
          <table width="100%">
            <tr class="row">
              <td><label for="bot_name"><span class="label">Bot Name</span></label></td>
              <td><span class="formw"><input class="fm-req" id="bot_name" name="bot_name" value="testBot" type="text"></span></td>
            </tr>
            <tr class="row">
              <td><label for="bot_active"><span class="label">Bot Active</span></label></td>
              <td>
                <span class="formw">
                  <select name="bot_active" id="bot_active">
                    <option value="1" [sel_yes]>Yes</option>
                    <option value="0" [sel_no]>No</option>
                  </select>
                </span>
              </td>
            </tr>
            <tr class="row">
              <td><label for="format"><span class="label">Response Format</span></label></td>
              <td>
                <span class="formw">
                  <select name="format" id="format">
                    <option value="html" [sel_html]>HTML</option>
                    <option value="xml" [sel_xml]>XML</option>
                    <option value="json" [sel_json]>JSON</option>
                  </select>
                </span>
              </td>
            </tr>
            <tr class="row">
              <td><label for="update_aiml_code"><span class="label">Update PHP Code in DB each turn</span></label></td>
              <td>
                <span class="formw">
                  <select name="update_aiml_code" id="update_aiml_code">
                    <option value="1" [sel_fyes]>yes</option>
                    <option value="0" [sel_fno]>no</option>
                  </select>
                </span>
              </td>
            </tr>
            <tr class="row">
              <td><label for="remember_up_to"><span class="label">Bot memory lines</span></label></td>
              <td>
                <span class="formw">
                  <input class="fm-req" id="remember_up_to" name="remember_up_to" value="10" type="text">
                </span>
              </td>
            </tr>
            <tr class="row">
              <td><label for="debugemail"><span class="label">Debug Email</span></label></td>
              <td>
                <span class="formw">
                  <input class="fm-req" id="debugemail" name="debugemail" value="0" type="text">
                </span>
              </td>
            </tr>
            <tr class="row">
              <td><label for="debugshow"><span class="label">Debugshow</span></label></td>
              <td>
                <span class="formw">
                  <select name="debugshow" id="debugshow">
                    <option value="0" [ds_]>source code view - show debugging in source code</option>
                    <option value="1" [ds_i]>file log - log debugging to a file</option>
                    <option value="2" [ds_ii]>page view - display debugging on the webpage</option>
                    <option value="3" [ds_iii]>email each conversation line (not recommended)</option>
                  </select>
                </span>
              </td>
            </tr>
          </table>
        </div>
        <div class="rightHalf noBorder">
          <table width="100%">
          <tr class="row">
            <td><label for="bot_desc"><span class="label">Bot Desc</span></label></td>
            <td><span class="formw">
              <textarea id="bot_desc" name="bot_desc">Bot used to test the installation of Program O</textarea>
            </span></td>
          </tr>
          <tr class="row">
            <td><label for="bot_parent_id"><span class="label">Bot Parent ID</span></label></td>
            <td><span class="formw">
              <select name="bot_parent_id" id="bot_parent_id">
[parent_options]
              </select>
            </span></td>
          </tr>
          <tr class="row">
            <td><label for="save_state"><span class="label">Save State</span></label></td>
            <td><span class="formw">
              <select name="save_state" id="save_state">
                    <option value="session" [sel_session]>Session</option>
                    <option value="database" [sel_db]>Database</option>
              </select>
            </span></td>
          </tr>
          <tr class="row">
            <td><label for="conversation_lines"><span class="label">Chat Lines To Display</span></label></td>
            <td><span class="formw">
              <input class="fm-req" id="conversation_lines" name="conversation_lines" value="10" type="text">
            </span></td>
          </tr>
          <tr class="row">
            <td><label for="debugmode"><span class="label">Debugmode</span></label></td>
            <td><span class="formw">
              <select name="debugmode" id="debugmode">
                  <option value="0" [dm_]>Show no debugging</option>
                  <option value="1" [dm_i]>general + errors</option>
                  <option value="2" [dm_ii]>general + errors + sql</option>
                  <option value="3" [dm_iii]>show everything</option>
              </select>
            </span></td>
          </tr>
          </table>
        </div>
        <input id="bot_id" name="bot_id" value="1" type="hidden">
        <div id="fm-submit" class="row fm-req center">
          <input name="action" id="action" value="update" type="submit">
        </div>
      </form>
    </fieldset>
  </body>
</html>