<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
  xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <xsl:output method="html" omit-xml-declaration="yes"/>

  <xsl:template match="/Settings">
    <div id="MainBody">
      <h1>Account Settings</h1>
      <div id="SettingsLeft">  
	<ul id="SettingsTabs">
	  <li class="active">General</li>
	  <li>Password</li>
	</ul>
      </div>
      <div id="SettingsRight">
        <div class="SettingsContent active" id="General">
	  <h2>General Account Settings</h2>
	  <form id="GeneralSettings" autocomplete="off" method="post" action="" >
	    <table>
	      <tr>
		<td class="label"><label id="lfname" for="fname">First Name</label></td>
		<td class="field">
		  <input disabled="disabled" id="fname" name="fname" type="text" value="{./Viewer/Member/@firstName}" data-default="{./Viewer/Member/@firstName}" maxlength="100" placeholder="First" />
		</td>
		<td><span class="EditButton">edit</span></td>
		<td class="status"></td>
	      </tr>
	      <tr>
		<td class="label"><label id="llname" for="lname">Last Name</label></td>
		<td class="field">
		  <input disabled="disabled" id="lname" name="lname" type="text" value="{./Viewer/Member/@lastName}" data-default="{./Viewer/Member/@lastName}" maxlength="100" placeholder="Last" />
		</td>
		<td><span class="EditButton">edit</span></td>
		<td class="status"></td>
	      </tr>
	      <tr>
		<td class="label"><label id="lemail" for="email">Email</label></td>
		<td class="field">
		  <input disabled="disabled" id="email" name="email" type="text" value="{./Viewer/Member/@emailAddress}" data-default="{./Viewer/Member/@emailAddress}" maxlength="150" placeholder="Email" />
		</td>
		<td><span class="EditButton">edit</span></td>
		<td class="status"></td>
	      </tr>
	      <tr>
		<td class="label"><label id="lbirthdate" for="birthdate">Birthdate</label></td>
		<td class="field">
		  <div class="date" id="monthcontainer">
		    <select disabled="disabled" id="month" name="month">
		      <option value="{./DateObject/@longMonth}" disabled="disabled" selected="selected" style="display:none;"><xsl:value-of select="./DateObject/@longMonth" /></option>
		      <option value="January">January</option><option value="February">February</option><option value="March">March</option>
		      <option value="April">April</option><option value="May">May</option><option value="June">June</option>
		      <option value="July">July</option><option value="August">August</option><option value="September">September</option>
		      <option value="October">October</option><option value="November">November</option><option value="December">December</option>
		    </select>
		  </div>
		  
		  <div class="date" id="daycontainer">
		    <select disabled="disabled" id="day" name="day">
		      <option value="{./DateObject/@date}" disabled="disabled" selected="selected" style="display:none;"><xsl:value-of select="./DateObject/@date" /></option>
		      <option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option>
		      <option value="6">6</option><option value="7">7</option><option value="8">8</option><option value="9">9</option><option value="10">10</option>
		      <option value="11">11</option><option value="12">12</option><option value="13">13</option><option value="14">14</option><option value="15">15</option>
		      <option value="16">16</option><option value="17">17</option><option value="18">18</option><option value="19">19</option><option value="20">20</option>
		      <option value="21">21</option><option value="22">22</option><option value="23">23</option><option value="24">24</option><option value="25">25</option>
		      <option value="26">26</option><option value="27">27</option><option value="28">28</option><option value="29">29</option><option value="30">30</option>
		      <option value="31">31</option>
		    </select>
		  </div>
		  
		  <div class="date" id="yearcontainer">
		    <select disabled="disabled" id="year" name="year">
		      <option value="{./DateObject/@year}" disabled="disabled" selected="selected" style="display:none;"><xsl:value-of select="./DateObject/@year" /></option>
		      <xsl:for-each select="years/year">
			<option value="{.}"><xsl:value-of select="." /></option>
		      </xsl:for-each>
		    </select>
		  </div>
	      </td>
	      <td><span class="EditButton">edit</span></td>
	      <td class="status"></td>
	      </tr>
	      <tr>
		<td><button id="SaveProfile" class="standard Blue">Save</button></td>
		<td class="SaveStatus"></td>
	      </tr>
	    </table>
	  </form>
	</div>
	
        <div class="SettingsContent" id="Password">
	  <h2>Change Your Password</h2>
	  <form id="PasswordSettings" autocomplete="off" method="post" action="" >
	    <table>
	      <tr><td>Current Password</td><td><input id="curpass" type='password' name="curpass"/></td><td class="status"></td></tr>
	      <tr><td>New Password</td><td><input id="newpass" type='password' name="newpass"/></td><td class="status"></td></tr>
	      <tr><td>Confirm New Password </td><td><input id="confirmpass" type='password' name="confirmpass"/></td><td class="status"></td></tr>
	      <tr>
		<td><button id="SavePass" class="standard Blue">Save</button></td>
		<td class="SaveStatus"></td>
	      </tr>
	    </table>
	  </form>
	</div>

      </div>
      <div class="clear"></div>
    </div>
  </xsl:template>

</xsl:stylesheet>
