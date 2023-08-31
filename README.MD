PHP implementation for Assessments system
==========================

Consider following requirements:
1. The system allows the recording of assessments carried out with their
   evaluations.
2. The evaluation is carried out by the Supervisor.
3. The evaluation is carried out offline.
4. Assessment is carried out in the indicated Standard.
5. Clients can have multiple assessments in different standards.
6. The Client being evaluated must have an active contract with the Supervisor.
7. The Supervisor must have authority for the standard on the day evaluation
   takes place.
8. Upon completion of evaluation the assessment can have positive or negative
   ratings.
9. The assessment has an expiration date of 365 days counting from the day
   evaluation took place. After it is exceeded, the assessment expires.
10. It is possible to lock the assessment by suspension or withdrawn.
11. Suspended assessment can be unlocked.
12. Suspended assessment may be withdrawn.
13. Withdrawned assessment cannot be unlocked nor lock cannot be changed into
    suspension.
14. Expired assessment cannot be locked.
15. It is not possible to lock an assessment that is currently locked, it is necessary to
    unlock it in advance. Only changing Suspension into withdrawn is allowed.
16. Assessment lock should contain descriptive information about the operation
    performed.
17. Conducting further evaluation is carried out under the same standard. This
    means that it is possible to replace an assessment obtained in the standard X by
    obtaining an assessment in the same standard X
18. If Client has an active assessment, the newly obtained assessment replaces the
    current one.
19. Subsequent evaluation may be conducted after a period of not less than 180
    days for evaluation completed with a positive result and 30 days for evaluation
    completed with a negative result

## ***Running tests***
```
composer run run-tests
```

## ***Running cs fixer***
```
composer run run-fixer
```

## Final clues 

1. If I had more to to model it I would consider to create factories for the tests fot minimize the number of code.
2. PHP CS fixer would be defined with my own rules if the requirements would have them.
3. I should have start from unit tests and write in TDD but after the first iteration I already created code and did not have time to start over.
4. I tried to cover all the requirements but I would need to task with an expert to resolve some questions that appeared during the implementation. 