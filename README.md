# Line Item Editor

This extension allows a staff user to add, edit and delete line items of a contribution.

Installation
------------

1. As part of your general CiviCRM installation, you should set a CiviCRM Extensions Directory at Administer >> System Settings >> Directories.
2. As part of your general CiviCRM installation, you should set an Extension Resource URL at Administer >> System Settings >> Resource URLs.
3. Navigate to Administer >> System Settings >> Manage Extensions.
4. Beside **Edit Line Item Extension** click Install.

Usage
-----

Each item in the contribution is now displayed with edit and cancel icons.
![image](https://user-images.githubusercontent.com/13468905/30987931-a62977d8-a466-11e7-91ee-8521ab13f368.png)

Edit opens the line item in a dialogue with the editable fields.
![image](https://user-images.githubusercontent.com/13468905/30990046-d7e7da56-a46d-11e7-9cf6-3f6b309df41d.png)

If the total contribution amount is increased, the contribution status is changed to **Pending payment** and a popup appears reminding the user to create a payment record.

If the total contribution amount in decreased, the contribution status changed to **Pending refund** and **Record refund** appears under More on the contribution record.

Clicking on the arrow to the left of the contribution amount or the amount link displays the history of the contribution with edits.

![image](https://user-images.githubusercontent.com/13468905/30990046-d7e7da56-a46d-11e7-9cf6-3f6b309df41d.png)

After the refund or additional payment is recorded, the contribution status is set to **Completed**
