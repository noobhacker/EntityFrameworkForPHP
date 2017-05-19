# EntityFrameworkForPHP
Simple QueryBuilder like ASP.NET Entity Framework.
![alt_text](https://github.com/noobhacker/EntityFrameworkForPHP/blob/master/img/preview.PNG)

I created this layer for my own project use and I find it very useful. 
This is not a super big many function framework but I build the crucial features for simple LINQ operation. 

![alt_text](https://github.com/noobhacker/EntityFrameworkForPHP/blob/master/img/semilambda.PNG)

Few reasons I didn't apply lambda expressions
1. Reflection in php? There is no way to parse .where(x => x.id == 1) into values, it will directly turn into boolean.
2. There is no => arrow in php, if you want lambda it would look like 

.where(function ($x) { $x.id == 1 }; ); //which is nonsense

![alt_text](https://github.com/noobhacker/EntityFrameworkForPHP/blob/master/img/update.PNG)

Package includes
- 1 core DbSet code file for query builder and 1 BaseContext for mysqli $conn DI.
- Template for DbContext and DbSet, since there is no DbSet<<Model>> in php you have to extend DbSet
- NO $db->saveChanges(), this is only query builder that looks like Entity Framework.

### Database naming conventions

- Table name -> plural 
- Primary key -> id
- Foreign key -> target_table_id

The join demonstrated above is for one to many only. I will do custom join like the one in C# if my project needs it.
