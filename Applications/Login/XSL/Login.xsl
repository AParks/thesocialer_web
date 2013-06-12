<?xml version="1.0"?>

<xsl:stylesheet version="1.0"
                xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" omit-xml-declaration="yes"/>

    <xsl:template match="/Login">
        <!--<div class="logosubtitle">A Better Way to Experience Your City</div>-->
        <div id="login">
            <div id="TrendingEvents" style="opacity:0">
                <ul class="EventList">
                    <xsl:call-template name="NearbyEventSkeleton" />
                </ul>
            </div>
            
            <div id="registerform">
                <h3>Cool People, Cool Places</h3>
                Get a better way to experience your city. It's free.
                <hr/>
                <div class="inner">
                    <form id="registration" autocomplete="off" method="post" action="" >
                        <div id="fullname">
                            <input id="firstname" name="firstname" type="text" maxlength="100" placeholder="First Name" />
                            <input id="lastname" name="lastname" type="text" maxlength="100" placeholder="Last Name" />
                        </div>
                       
                        <input id="email" name="email" type="text" maxlength="150" placeholder="Email" />   
                        <input id="password" name="password" type="password" maxlength="50" value="" placeholder="Password" />
                        <input type="checkbox" id= "passcheck"/> 
                        <label for="passcheck">Show password </label>

                        <input id="college" name="college" type="text" placeholder="Where did you go to school?" />
                        <label id="lbirthdate" for="birthdate">Birthday:</label>
                        <div>
                            <div id="monthcontainer">
                                <select type="month" id="month" name="month">
                                    <option value="" disabled="disabled" selected="selected" style="display:none;">Month</option>
                                    <option value="January">January</option>
                                    <option value="February">February</option>
                                    <option value="March">March</option>
                                    <option value="April">April</option>
                                    <option value="May">May</option>
                                    <option value="June">June</option>
                                    <option value="July">July</option>
                                    <option value="August">August</option>
                                    <option value="September">September</option>
                                    <option value="October">October</option>
                                    <option value="November">November</option>
                                    <option value="December">December</option>
                                </select>
                            </div>
		    
                            <div id="daycontainer">
                                <select type="day" id="day" name="day">
                                    <option value="" disabled="disabled" selected="selected" style="display:none;">Day</option>
                                    <option value="1">1</option>
                                    <option value="2">2</option>
                                    <option value="3">3</option>
                                    <option value="4">4</option>
                                    <option value="5">5</option>
                                    <option value="6">6</option>
                                    <option value="7">7</option>
                                    <option value="8">8</option>
                                    <option value="9">9</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                    <option value="13">13</option>
                                    <option value="14">14</option>
                                    <option value="15">15</option>
                                    <option value="16">16</option>
                                    <option value="17">17</option>
                                    <option value="18">18</option>
                                    <option value="19">19</option>
                                    <option value="20">20</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="31">31</option>
                                </select>
		     
                            </div>
		    
                            <div id="yearcontainer">
                                <!--<input tabindex="7" id="year" name="year" placeholder="year" />-->
                                <select type="year" id="year" name="year">
                                    <option value="" disabled="disabled" selected="selected" style="display:none;">Year</option>
                                    <xsl:for-each select="years/year">
                                        <option value="{.}">
                                            <xsl:value-of select="." />
                                        </option>
                                    </xsl:for-each>
                                </select>
                            </div>
                        </div>
                        <label id="lgender" for="gender">Gender:</label>
                        
                        <div id="gendercontainer">
                            <label>
                                <input type="radio" name="gender" value="male" /> Male</label>
                            <label>
                                <input type="radio" name="gender" value="female" /> Female</label>
                        </div>
                        <label id="lloc" for="location">Location:</label>
                        <div id="loccontainer">
                            <label>
                                <input type="radio" name="location" value="Philadelphia" checked="checked" /> Philadelphia</label>
                            <label>
                                <input type="radio" name="location" value="Other" /> Other</label>
                        </div>
                        <input class="joinbutton" id="signupsubmit" name="signup" type="submit" value="Join" />
                    </form>
                </div>
            </div>

        </div>

    </xsl:template>

    <!--<xsl:template name="NearbyEventSkeleton">
      <li class="nearbyevent nearbyeventskeleton">
        <div class="nearbyeventimage"></div>
        <div class="nearbyeventname"></div>
        <div class="nearbyeventlocation"></div>
        <ul class="nearbyeventattendees"></ul>
        <span class="nearbyeventattendeeCount"></span>
        <div class="clear"></div>
      </li>
    </xsl:template>-->
  
    <xsl:template name="NearbyEventSkeleton">
        <li class="hide LocationSkeleton">
            <div class="Event">
                <div class="AttendingButtonsHolder">
                    <ul class="AttendingCounts">
                        <li class="AttendingCount"></li>
                    </ul>
                </div>
                <div class="LocationImage">
                    <div class="TitleLocationContainer">
                        <div class="EventTitle"></div>
                        <div class="EventLocation"></div>
                    </div>
                </div>
            </div>
        </li>
    </xsl:template>
  
</xsl:stylesheet>
