=== Front End Course Creation Plugin for LearnDash ===
Current Version: 2.2.1
Author:  WisdmLabs
Author URI: https://wisdmlabs.com/
Plugin URI: https://wisdmlabs.com/front-end-course-creation-for-learndash/
Tags: LearnDash Add-on, Instructor Role LearnDash, User Role LearnDash
Requires at least: 4.7
Tested up to: 4.9.1
License: GNU General Public License v2 or later

Tested with LearnDash version: 2.5.2
Tested with BuddyPress version: 2.9.2

== Description ==
Want users to create courses without providing them back-end access? We’ve got a solution for you! With the Front-end Course Creation Plugin, you can create a ‘Course Author’ user with special privileges. A Course Author can create and manage courses, lessons, topics and quizzes, right from the front-end!


== Installation Guide ==
1. Upon purchasing the Front-end Course Creation plugin, an email will be sent to the registered email id, with the download link for the plugin and a purchase receipt id. Download the plugin using the download link.
2. Go to Plugin-> Add New menu in your dashboard and click on the ‘Upload’ tab. Choose the ‘fcc-learndash.zip’ file to be uploaded and click on ‘Install Now’.
3. After the plugin has installed successfully, click on the Activate Plugin link or activate the plugin from your Plugins page.
4. An Frontend Course Creation License sub-menu will be created under Plugins menu in your dashboard. Click on this menu and enter your purchased product’s license key. Click on Activate License. If license is valid, an ‘Active’ status message will be displayed, else ‘Inactive’ will be displayed.
5. Upon entering a valid license key, and activating the license, you will find a ‘Course Author’ user role created, and ‘Course Creation Settings’ menu added to LearnDash settings.

== User Guide ==
Upon installing and activating the Front-end Course Creation plugin for LearnDash, a ‘Course Author’ role is added.

1. Creating a Course Author
To create a Course Author, add a new user by heading over to Users->Add New, and set the user role as ‘Course Author’.

Tip: You can also assign existing course content to Course Authors, by setting the ‘Author’ of the Course/Lesson/Topic/Quiz to a Course Author

2. Course Creation Settings
Under LearnDash->Settings, you should notice a ‘Course Creation Setting’ tab added. Under this tab, you have the option to auto-publish course content added by ‘Course Authors’, or save the content as drafts.

Remember to click ‘Save Settings’ once any changes have been made.

3. Display Course Creation Options
The FCC plugin automatically creates and adds course content creation pages:

Course Content Creation Pages: Allows a ‘Course Author’ to create Courses, Lessons, Topics, Quizzes and Add Quiz Questions.
Create Course
Create Lesson
Create Topic
Create Quiz
Create Question

Course Management Pages: These pages allow Course Authors to view the course, lessons, topics, quizzes and questions they have created and make any changes using the ‘Edit’ option.
Course List
Lesson List
Topic List
Quiz List
Question List
Link these pages from your website’s menu to allow Course Authors to create and manage course content from the front-end

4. Front-end Settings in BuddyPress
The plugin provides a quick link option ‘Listing’ right from the Course Author’s BuddyPress profile page to allow course content to be created and managed.


== Features ==
1. Course Content Creation Capability
2. Quiz Creation Capability
3. Block Backend Access
4. Save as Draft or Auto-Publish Content
5. Auto-Generated Course Creation Pages
6. BuddyPress Compatible


== Changelog ==
=2.2.1=
1. Fixes for fatal errors due to get_settings_error and get_current_screen methods.

=2.2.0=
1. Added major compatibility bug fixes with Learndash v3.0

=2.1.6=
1. Translation for some new strings is added in this update. 
2. Added hooks and filters for customization. 
3. Updated License Code.
4. Compatibility with the Leardash 2.5.7.
5. Compatibility with WooCommerce to Purchase LD courses as products

=2.1.5=
1. Compatibility with eLumine theme
2. Compatibility with LearnDash 2.5.4

=2.1.4=
*Compatibility with LD2.5.2*
1. Added LD course builder on frontend course edit page
2. Support for shared course steps
3. Fix: Fixed translation issues

=2.1.3=
1. Fix: Assigned Lessons dropdown in quiz listing page will show lessons and topics also and just lessons
2. Fix: Setting “Different points for each answer” option for Essay type Question removed

=2.1.2=
1. Improvement: Added Support for Video Progression feature for Lessons and Topics.
2. Fix: Course Author will need to filter quizzes by name or associated course.
3. Fix: Course Author should not check “Different points for each answer” field for  Essay type Question.

=2.1.1=
1. Fix: Issues with Commission feature fixed
2. Fix: View/Edit/Delete icons conflict with themes like University, Pathshala etc. fixes
3. Fix: If Course/Lesson/Topic title contain special characters(@,<,&,$,%,etc) then Course/Lesson/Topic title was not showing on Data Table 	 Filter DropDown
4. Fix: If page contains any custom field with id=’title’ then it was checking if that field is empty. Changed the id to make it unique

=2.1.0=
1. Compatibility with LearnDash 2.4.x
2. Support for LearnDash Post Specific Categories and Tags.
3. Provided options for setting both the LearnDash as well as WordPress categories and tags in the course, lesson and topic add/edit page.
4. The categories and tags options will be displayed based on the setting in the backend.
5. Provided setting in the quiz edit page to set the featured image of the quiz.
6. Added setting for Course Points in the course add/edit page
7. Modified the Make Lesson Visible on Specific Date option in the lesson add/edit page.>/li>

*Actions in the Landing Page*

8. Link to move a course, lesson, topic or a quiz into the trash and later changed to restore.
9. Link to view the courses, lessons, topics and quizzes.
10. Link to list all the lessons and quizzes associated with the course in the all courses table.
11. Link to list all the topics and quizzes associated with the lesson in the all lessons table.
12. Link to list all the questions in the quiz.

*Other Features and Improvements*

13. New: Displayed “Associated Contents” section in each course, lesson, topic and quiz edit page.
14. New: Added button to remove questions from the quiz.
15. Improvement: Optimized the existing code and changed the previous approach of storing data in order to avoid the data duplication.
16. Improvement: Removed the Questions tab and provided links to view and add/edit question from the quiz edit page. So the navigation becomes faster.
17. Improvement: Added Compatibility as per quiz setting for Message with the correct/incorrect answer on question add/edit page.
18. Improvement: Added Compatibility as per quiz setting for Message with the correct/incorrect answer on question add/edit page.
19. Improvement: Updated filters on Question listing page now we can filter questions by name, question type and question categories.
20. Improvement: Compatibility with PHP 7.x
21. Improvement: License code updated
22. Fix: Added categories option in the quiz add/edit page.
23. Fix: Added option for View Profile Statistics in the quiz add/edit page.
24. Fix: Fixed the add media for answers issue in questions.
25. Fix: Fixed the Show Points field saving issue in the quiz add/edit page>/li>
26. Fix: Fixed the Quiz-summary field saving issue in the quiz add/edit page.
27. Fix: Fixed the Fill in the Blank questions different points for each answer issues for multiple answers.
28. Fix: Added Hide Course Content table field on course add/edit page.

=2.0.3=
* Fixes
1. Fixed warning appearing for non-logged in users


=2.0.2=
*Fixes
1. Fixed the fatal error on quiz creation page

=2.0.1=
*Fixes
1. Licence code updated to avoid conflicts with namespaces

=2.0.0=
*Fixes
1. Made Buddypress menu visible to course author
2. Made Compatible with latest buddypress
3. Associated lesson in quiz add/edit page
4. Featured Image for Course, Lesson, Topic solved
5. Question points being set as 1 each time
6. Display of save button on 'Sort' question type
7. Description field for course, lesson and topic made optional
8. Solved all the translation ready issues
9. Quiz pre-requisite was not being set
10. Licence code is updated

*Updates
1. Added 'Essay' question type depending upon
2. Added new fields according to Learndash Version (Award Points for Assignments, Number of ponits for assignments, 'Display answers randomly' in Quiz creation, 'Correct and incorrect mark' in quiz creation)
3. Restricted Course author to access backend if only 'Course Author' role is associated with the user
4. Made admin bar visible to the course author
5. Added filter to redirect to the link in case 'Course Author' is trying to access backend

=1.1.5=
*Updates
1. Added the commission tab on frontend

=1.1.4=
Bug Fixes

=1.1.3=
*Video can be added at the front-end
*Made translation Ready

=1.1.2=
*No permissions error message fixed
*Edit option at the bottom at front-end removed

=1.1.1=
*Preview feature added
*Course count bug fixed
*Course not created in the front-end bug fixed

= 1.1.0 =
* Updated with Commissions Feature for Course Author

= 1.0 =
* Plugin Released

== FAQ ==

For how long is a license valid?
Every license is valid for a year from the date of purchase. During this year you will receive free support. After the license expires you can renew the license for a discounted price.

What will happen if my license expires?
If your license expires, you will still be able to use the plugin, but you will not receive any support or updates. To continue receiving support for the plugin, you will need to purchase a new license key.

Is the license valid for more than one site?
Every purchased license is valid for one site. For multiple site, you will have to purchase additional license keys.

Help! I lost my license key!
In case you have misplaced your purchased product license key, kindly go back and retrieve your purchase receipt id from your mailbox. Use this receipt id to make a support request to retrieve your license key.

How do I contact you for support?
You can direct your support request to us, using the Support form on our website.

Do you have a refund policy?
Refunds will be granted only if CSP does not work on your site and has integration issues, which we are unable to fix, even after support requests have been made.

Refunds will not be granted if you have no valid reason to discontinue using the plugin. CSP only guarantees compatibility with the
WooCommerce plugin. Refund requests will be rejected for reasons of incompatibility with third party plugins.

Kindly refer to https://wisdmlabs.com/front-end-course-creation-for-learndash/ for additional details.

