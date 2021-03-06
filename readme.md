## Parliament Petition Monitor

More documentation to come over the next few days.

This is a Laravel 5.7 application.
Its purpose is to record the counts of votes on a UK Parliament
open petition at regular intervals to allow trends to be analysed.

Current open petitions can be found here:

https://petition.parliament.uk/petitions?state=open

## Artisan Commands

* `petition:create-petition` - Add a petition, or update a petition metadata
* `petition:fetch-votes` - Fetch and store current vote counts for a petition.
* `petition:fix-data` - temporary data fixer as the data model is refined in early stages.

## TODO

This list will likely grow:

* [ ] Document commands.
* [x] Set schedule in data.
* [ ] Support enable/disable flag.
* [ ] Disable a schedule when a petition closes.
* [ ] Front-end reporting and data export.
* [ ] Installation instructions.
* [ ] Automatically update petition metadata on any substantive change (e.g. milestones).
* [ ] With lots of jobs scheduled, it may be worth running them from a queue
      so jobs could be run in parallel.

