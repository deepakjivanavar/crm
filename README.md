nectarcrm CRM
==========

nectarcrm is a PHP based web application that enables businesses to increase sales wins, marketing ROI, and support satisfaction by providing tools for employees and management work more effectively, capture more data, and derive new actionable insights from across the customer lifecycle.

Get involved
------------

Development on nectarcrm is done at http://code.nectarcrm.com

**Note**: Any contributions submitted to nectarcrm project should be made available under nectarcrm Public License. 
If contribution has any patented code, or commercial code, then please communicate with nectarcrm team before making the contribution.

https://www.nectarcrm.com/nectarcrm-public-license/

To register for an account, please contact info @ nectarcrm.com, you will need this to file issues and/or fix the code
Once you have an account, you can [browse the code](http://code.nectarcrm.com/nectarcrm/nectarcrmcrm/tree/master),
[see if your issue is already reported](http://code.nectarcrm.com/nectarcrm/nectarcrmcrm/issues) and if you have a new problem
to report you can [create an issue](http://code.nectarcrm.com/nectarcrm/nectarcrmcrm/issues/new?issue)

If you then want to fix the issue (or another issue) you can create your own fork of nectarcrm to work on using the
fork button on the nectarcrm project, this will create a new git repository for you at
    
    http://code.nectarcrm.com/yourname/nectarcrmcrm.git

on your computer you will need a git client installed and you need to tell git who you are:

    git config --global user.name "YOUR NAME"
    git config --global user.email "YOUR EMAIL ADDRESS"

now clone your fork of nectarcrm

    git clone http://code.nectarcrm.com/yourname/nectarcrmcrm.git

this will pull down from the server your copy of the nectarcrm code and all the history.

You will make a new branch for your changes, you can give it a descriptive name, once the branch is created
you will switch to that branch using the checkout command

    git branch fix_projects_on_calendar
    git checkout fix_projects_on_calendar

Now you can make your changes and commit all changed files with

    git commit -a

Do reference the issue number in your commit message, e.g. "fix #2 display projects on the calendar" the number will
allow the system to link the commit to the issue.

Now you can push your branch to the server, this creates the branch on the server end and populates it

    git push --set-upstream origin fix_projects_on_calendar

look at the branch on code.nectarcrm.com and create a merge request from your branch
to the upstream master, this will be reviewed to see if it fixes the 
issue and if all is good will be merged into the upstream code.
You can then switch back to your master branch with

    git checkout master

And you can create additional feature branches from there to fix different things.

If there have been other changes to the central nectarcrm code that you want in your work area then you can add the central
repository as an upstream remote (only need to do this bit once), then you can fetch changes and merge them

    git remote add upstream http://code.nectarcrm.com/nectarcrm/nectarcrmcrm.git
    git fetch upstream
    git merge upstream/master

