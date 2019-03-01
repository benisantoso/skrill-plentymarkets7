<div class="alert alert-warning" role="alert">
    <strong><i>Important:</i></strong> The Skrill plugin has been developed for use with the online store Ceres and only works with its structure or other template plugins. IO plugin is required. For the best compatibility please use IO/Ceres with version 1.4.0 - 3.0.1.
</div>
<br>
<div class="alert alert-warning" role="alert">
    <strong><i>Important:</i></strong> Please make sure that the Skrill plugin position is less than IO/Ceres position. Set IO/Ceres plugins position to 99 to avoid this problem.
</div>

Plugins that you buy on plentyMarketplace will be saved in the Plugins » Purchases menu of your plentymarkets system. Proceed as described below to install the purchased plugins.

## Installing plugins

After purchasing a plugin on plentyMarketplace, the plugin will be available in the Plugins <strong>»</strong> Purchases tab, where it can be installed.

#### Installing plugins:
<ul>
	<li>Go to Plugins »Purchases.</li>
	<li>→ All plugins purchased on plentyMarketplace will be displayed here.</li>
	<li>Click on Install plugin in the line of the plugin.</li>
	<li>→ The Install plugin window will open.</li>
	<li>Select the plugin version from the drop-down menu.</li>
	<li>→ The newest plugin version is pre-selected.</li>
	<li>Click on Install.</li>
	<li>→ The plugin will be installed and available in the plugin overview.</li>
</ul>

## Deploying plugins

The Plugins » Plugin overview menu serves as your inbox for plugins. From there, you deploy your plugins in Productive.

Deploying plugins in Productive:
<ul>
	<li>Go to Plugins » Plugin overview.</li>
	<li>Activate or deactivate plugins in Productive.</li>
	<li>→ Changes made after the last deployment will be highlighted in the menu bar.</li>
	<li>Point your cursor over the More button.</li>
	<li>→ The context menu will open.</li>
	<li>Click on Select clients.</li>
	<li>→ The Select clients window will open.</li>
	<li>Activate the clients.</li>
	<li>Save the settings.</li>
	<li>→ The activated clients will be linked.</li>
	<li>Click on Deploy plugins in Productive in the menu bar.</li>
	<li>→ The activated plugins are checked and deploy process is started.</li>
	<li>→ The colour of the icon of deployed plugins changes to green.</li>
</ul>

The settings that are available in the menu bar are explained in the following table.

<table>
	<thead>
		<th>
			Setting
		</th>
		<th>
			Explanation
		</th>
	</thead>
	<tbody>
		<tr>
			<td>
				Switch to table view; Switch to card view
			</td>
			<td>Click to switch between card view and table view.</td>
		</tr>
		<tr>
			<td>
				Deploying plugins in Stage
			</td>
			<td>Checks all activated plugins and deploys them in the preview mode Stage. In this mode, a preview of the active plugins is deployed to allow you to test their functionality. Changes made after the last deployment are highlighted in yellow.
			<br>
			<i>Note:</i> All plugins marked as active are compiled sequentially during deployment of plugins in Stage. This may take several minutes. If an error occurs, none of the plugins are deployed.</td>
		</tr>
		<tr>
			<td>
				Plugin log (Stage)
			</td>
			<td>Overview of deployment processes of plugins in Stage. Failed processes will be highlighted. Errors that occurred during the last deployment of plugins in Stage will be displayed in a list.</td>
		</tr>
		<tr>
			<td>
				Deploy plugins in Productive
			</td>
			<td>Checks all plugins and deploys them in the productive mode Productive. In this mode, active plugins are run and published. Changes made after the last deployment are highlighted in yellow.
			<br>
			<i>Note:</i> All plugins marked as active are compiled sequentially during deployment of plugins in Productive. This may take several minutes. If an error occurs, none of the plugins are deployed.</td>
		</tr>
		<tr>
			<td>
				Plugin log (Stage)
			</td>
			<td>Overview of deployment processes of plugins in Productive. Failed processes will be highlighted. Errors that occurred during the last deployment of plugins in Stage will be displayed in a list.</td>
		</tr>
		<tr>
			<td>
				Switch to Stage; Switch to Productive
			</td>
			<td>Switch to Stage = Change the mode from the productive mode Productive to the preview mode Stage. All plugins deployed in Stage will be displayed when opening the online store in the browser. The plugins are only visible to the users of your plentymarkets system. The mode will be set back to Productive automatically after 120 minutes.
				<br>
			<i>Important:</i> The mode of deployed plugins will be changed to the preview mode; the data base, however, will not be changed. With the respective permissions, these plugins can access, change or even delete data in the data base of your productive system.
			<br>
			Change to Productive = Change the mode from Stage back to Productive.</td>
		</tr>
		<tr>
			<td>
				Deploy automatically
			</td>
			<td>Activate to deploy plugins in Stage automatically when updates are available. Plugins must already be active in Stage.</td>
		</tr>
	</tbody>
</table>

## Updating plugins

Plugin updates will be shown both on plentyMarketplace and in the plentymarkets back end. For new available versions the Update plugin flag will be displayed in the upper right corner of a plugin card in the plugin overview. Proceed as described below to install a new version of a plugin purchased on plentyMarketplace.

#### Updating plugins:
<ul>
	<li>Go to Plugins » Plugin overview.</li>
	<li>Point your cursor over the More button.</li>
	<li>→ The context menu will open.</li>
	<li>Click on Update plugin.</li>
	<li>→ The Update plugin window will open.</li>
	<li>Select the version from the drop-down menu.</li>
	<li>Click on Update plugin.</li>
	<li>→ The plugin will be updated to the new version.</li>
</ul>

To use the plugin in the updated version, deploy it again as described above.

<p style="color: red;">
	When updating a plugin, only newer versions of the plugin can be installed. If you want to reset the plugin to an older version, delete the plugin first and reinstall the plugin with the desired version.
</p>

## Setting up Skrill in plentymarkets

Before using the full functionality of the plugin, you first have to setting the general settings.

#### Managing general settings:
<ul>
	<li>Go to Settings » Orders » Skrill » General Settings.</li>
	<li>Select a Client (store).</li>
	<li>Carry out the settings.</li>
	<li>Save the settings.</li>
</ul>

<table>
	<caption>Pay attention to the information given in the table bellow</caption>
	<thead>
		<th>
			Setting
		</th>
		<th>
			Explanation
		</th>
	</thead>
	<tbody>
		<tr>
			<td>
				Merchant ID
			</td>
			<td>Your Skrill customer ID. It is displayed in the upper-right corner of your Skrill account.</td>
		</tr>
		<tr>
			<td>
				Merchant Account (email)
			</td>
			<td>Your Skrill account email address.</td>
		</tr>
		<tr>
			<td>
				Recipient
			</td>
			<td>A description to be shown on Quick Checkout. This can be your company name (max 30 characters).</td>
		</tr>
		<tr>
			<td>
				Logo-URL
			</td>
			<td>The URL of the logo which you would like to appear at the top right of the Skrill page. The logo must be accessible via HTTPS or it will not be shown. For best results use logos with dimensions up to 200px in width and 50px in height.</td>
		</tr>
		<tr>
			<td>
				Shop Url
			</td>
			<td>Your shop Url</td>
		</tr>
		<tr>
			<td>
				MQI/API Password
			</td>
			<td>When enabled, this feature allows you to issue refunds and check transaction statuses. To set it up, you need to login to your Skrill account and go to Settings -> then, Developer Settings</td>
		</tr>
		<tr>
			<td>
				Secret word
			</td>
			<td>This feature is mandatory and ensures the integrity of the data posted back to your servers. To set it up, you need to login to your Skrill account and go to Settings -> then, Developer Settings.</td>
		</tr>
		<tr>
			<td>
				Display
			</td>
			<td>iFrame – when this option is enabled the Quick Checkout payment form is embedded on your website, Redirect – when this option is enabled the customer is redirected to the Quick Checkout payment form . This option is recommended for payment options which redirect the user to an external website.</td>
		</tr>
		<tr>
			<td>
				Merchant Email
			</td>
			<td>Your email address to receive payment notification.</td>
		</tr>
	</tbody>
</table>

#### Managing payment methods:

<ul>
	<li>Go to Settings » Orders » Skrill » select payment method (eg : Visa, Giropay, Rapid Transfer, etc).</li>
	<li>Select a Client (store).</li>
	<li>Carry out the settings.</li>
	<li>Save the settings.</li>
</ul>

<table>
	<caption>Pay attention to the information given in the table bellow</caption>
	<thead>
		<th>
			Setting
		</th>
		<th>
			Explanation
		</th>
	</thead>
	<tbody>
		<tr>
			<td>
				Payment Method Name
			</td>
			<td>Payment method name in English and German</td>
		</tr>
		<tr>
			<td>
				Enabled
			</td>
			<td>Check mark to enable the payment method.</td>
		</tr>
		<tr>
			<td>
				Show separately
			</td>
			<td>Check mark to show separately the payment method.</td>
		</tr>
	</tbody>
</table>

## Automatically updating order status Skrill payments
Set up an event procedure to trigger update order status a Skrill payment.

#### Setting up an event procedure:
<ul>
	<li>Go to Settings » Orders » Event procedures.</li>
	<li>Click on Add event procedure. → The Create new event procedure window will open.</li>
	<li>Enter the name : Skrill Upate Order Status</li>
	<li>Select the event : Status change - [4] In preparation for shipping</li>
	<li>Save the settings.</li>
	<li>Place a check mark next to the option Active.</li>
	<li>Add Filter : Select Order - Payment method (check mark all Skrill payment methods)</li>
	<li>Add Procedures : Select Plugins - Update order status the Skrill-Payment</li>
	<li>Save the settings.</li>
</ul>

Then if you change order status from [3] Waiting for payment to [4] In preparation for shipping, Skrill update order status will be executed.

## Automatically refunding Skrill payments
Set up an event procedure to trigger refund a Skrill payment.

#### Setting up an event procedure:
<ul>
	<li>Go to Settings » Orders » Event procedures.</li>
	<li>Click on Add event procedure. → The Create new event procedure window will open.</li>
	<li>Enter the name : Skrill Refund</li>
	<li>Select the event : New credit note.</li>
	<li>Save the settings.</li>
	<li>Place a check mark next to the option Active.</li>
	<li>Add Filter : Select Order - Payment method (check mark all Skrill payment methods)</li>
	<li>Add Procedures : Select Plugins - Refund the Skrill-Payment</li>
	<li>Save the settings.</li>
</ul>
Then if you create a new credit note, Skrill refund will be executed.