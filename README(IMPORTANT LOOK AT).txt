README — Notes and Issues from Development

Hello Professor,

This README includes a few important explanations about bugs or technical issues I encountered that I could not fully resolve during development.

---

API Integration (JiuJitero)

This project is built around a Jiu Jitsu technique database, and I attempted to integrate data from the public JiuJitero API:
https://jiujitero-api.onrender.com/api/v1

The API is meant to provide data about athletes and academies. However, despite multiple integration attempts (using both file_get_contents and cURL), the API would consistently time out or return no data. This may be due to the hosting service putting the server to sleep, or the API becoming inactive altogether.

The API integration logic is still included in the project (see pages/jiujitero.php), in case it becomes available in the future or for evaluation purposes.

---

myPHPAdmin / MySQL Permission Issues

I ran into an issue with phpMyAdmin where I was unable to grant permissions to my MySQL user account due to a rare Aria storage engine bug ("Read page with wrong checksum"). This prevented me from connecting to my database initially.

After researching the issue, I was able to resolve it with a combination of privilege grants and restarting the database engine. Stack Overflow was extremely helpful in identifying and fixing this issue.

All pages are now fully operational.

---

Image Upload Notes

For image handling, I used the Intervention/Image PHP package. While trying to use the latest version (v3), I encountered breaking changes and compatibility issues. As a result, I reverted to version 2.7, which works reliably with the current setup.

If you or anyone testing this project installs a newer version of Intervention/Image, it will likely cause errors with the existing image processing logic (resizing, saving).

Also, some image uploads may fail if the file is in an unsupported or corrupted format. Basic validation is in place, but edge cases could still cause issues.

---

test.php and test.jpeg

During debugging and troubleshooting, I created `test.php` and `test.jpeg` to validate image uploads and server configuration outside the main application flow. These files helped confirm that file handling, GD support, and permissions were set up correctly.

They are left in the project intentionally to show the testing steps taken while solving upload and library-related problems.

---

Thank you for reviewing this project. I hope the explanations above make clear the efforts made, especially around troubleshooting real-world issues!!!! Thanks for your time!

**One more thing! If you are wanting to test admin user on the website, I made a mock admin account for you to sign into "TestCoach":
username:coachTest
password:testCoach
Enjoy!


****Deployment Issues, summary for what had to be done*****
During deployment for my website I ran into several issues that required some time to fix. The initial links provided in the course work simply did not work or caused issues (Would not accept my finalProject folders size, or database errors). I decided to use Heroku with the help of several classmates. One of the main challenges I ran into involved handling file paths and ensuring they aligned with Herokus file system structure and routing expectations. 

All internal file paths were reviewed and I updated to use relative URLs that are compatible with Herokus directory structure. 

Environment configuration was also adjusted to support Herokus JAWSDB_URL for database access (A huge issue I was encountering with other sites), this allowed the app to connect securely to the MySQL database. I hope this is acceptable. Thanks!
