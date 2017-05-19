# EntityFrameworkForPHP
Simple QueryBuilder like ASP.NET Entity Framework.
![alt_text](https://github.com/noobhacker/EntityFrameworkForPHP/blob/master/preview.PNG)

I created this layer for my own project use and I find it very useful. 
This is not a super big many function framework but I build the crucial features for simple LINQ operation. 

- 1 core DbSet code file for query builder and 1 BaseContext for mysqli $conn DI.
- NO $db->saveChanges(), this is only query builder that looks like Entity Framework.

### Database naming conventions

- Table name -> plural 
- Primary key -> id
- Foreign key -> target_table_id

The join demonstrated above is for one to many only. I will do custom join like the one in C# if my project needs it.
