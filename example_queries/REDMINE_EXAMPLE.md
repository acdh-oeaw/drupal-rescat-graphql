
1. Ingest Person
  - id: 1
  - identifiers: - paragraph
    - id: https://redmine.acdh.oeaw.ac.at/users/92
    - label: Peter Andorfer
    - type: redmine

    - id: peter.andorfer@oeaw.ac.at
    - label: Peter Andorfer
    - type: email
Identifier paragraphs are not working yet...

id: 85
```
mutation PersonMutation {
  createPerson(data: {title: "Peter Andorfer"}) {
    id
  }
}
```


2. Ingest Project
  actors:
  - personId: 1
    role: ProjectLeader
  - drupalUser: user1
  -endDate: null
  -id: 1
  -name: Auden-Musulin-Papers
  -redmineIssueId: 18626
  -startDate: '2021-01-08'

ID: 84.
```
mutation MyMutation {
  createProject(
    data: {headline: "Auden-Musulin-Papers", redmineId: 18626, startDate: "2021-01-08"}
  ) {
    id
    headline
    endDate
  }
}
```


3. Ingest Dataset

   - actors:
     - personId: 1
     - role: ContactPerson

   - description: "XML/TEI Files and Images (TIFF/JPGS) from the AMP-Project\r\n\r\n\
    XML/TEI (and ARCHE-MD for Images) are curated in \"GitHub\":https://github.com/Auden-Musulin-Papers/amp-data\r\
    \nImage Data is located at acdh_resources/container/R_amp_19479 #19479\r\n\r\n\
    PI is \"Sandra Mayer\":https://www.oeaw.ac.at/acdh/team/current-team/sandra-mayer\
    \ \r\n\r\n\r\nh2. some links\r\n\r\n* Link/path to the ARCHE metadata delivered\
    \ by the depositor.\r\n** MD is provided/curated by the depositors directly in\
    \ \"arche_constants.md\":https://github.com/Auden-Musulin-Papers/amp-data/blob/main/data/meta/arche_constants.rdf\r\
    \n** see this file's history for some kind of \"curation-log\":https://github.com/Auden-Musulin-Papers/amp-data/commits/main/data/meta/arche_constants.rdf\r\
    \n\r\n* Link/path to the file-checker and other reports (PDF checks) \u2013 with\
    \ a date to distinguish first and last check (I suppose in this case it is a part\
    \ of the GitHub process)\r\n**  as of now the filechecker is part of the \"GitHub\
    \ ingestion workflow\":https://github.com/Auden-Musulin-Papers/amp-data/actions/workflows/arche.yml\
    \ see for example \"this run\":https://github.com/Auden-Musulin-Papers/amp-data/runs/6861112184?check_suite_focus=true\
    \ if there were any issues found by the filechecker the ingestion workflow would\
    \ have stopped\r\n** but I think it would be nice to persist the logs of the filechecker,\
    \ I'll think about something\r\n\r\n* Link/path to feedback from the depositor\
    \ (if possible with documented changes that were already done and which not)\r\
    \n** same file/file-history as before\r\n\r\n* Link to the ingestion log.\r\n\
    ** ingestions logs are saved in the github-action runs"

   - id: 1
   - name: AMP Auden-Musulin-Papers
   - projectId: 1
   - redmineIssueId: 20352

```
mutation DatasetMutation {
  createDataset(
    data: {headline: "AMP Auden-Musulin-Papers", description: "XML/TEI Files and Images (TIFF/JPGS) from the AMP-Project\\r\\n\\r\\n\\     XML/TEI (and ARCHE-MD for Images) are curated in \\\"GitHub\\\":https://github.com/Auden-Musulin-Papers/amp-data\\r\\     \\nImage Data is located at acdh_resources/container/R_amp_19479 #19479\\r\\n\\r\\n\\     PI is \\\"Sandra Mayer\\\":https://www.oeaw.ac.at/acdh/team/current-team/sandra-mayer\\     \\ \\r\\n\\r\\n\\r\\nh2. some links\\r\\n\\r\\n* Link/path to the ARCHE metadata delivered\\     \\ by the depositor.\\r\\n** MD is provided/curated by the depositors directly in\\     \\ \\\"arche_constants.md\\\":https://github.com/Auden-Musulin-Papers/amp-data/blob/main/data/meta/arche_constants.rdf\\r\\     \\n** see this file's history for some kind of \\\"curation-log\\\":https://github.com/Auden-Musulin-Papers/amp-data/commits/main/data/meta/arche_constants.rdf\\r\\     \\n\\r\\n* Link/path to the file-checker and other reports (PDF checks) \\u2013 with\\     \\ a date to distinguish first and last check (I suppose in this case it is a part\\     \\ of the GitHub process)\\r\\n**  as of now the filechecker is part of the \\\"GitHub\\     \\ ingestion workflow\\\":https://github.com/Auden-Musulin-Papers/amp-data/actions/workflows/arche.yml\\     \\ see for example \\\"this run\\\":https://github.com/Auden-Musulin-Papers/amp-data/runs/6861112184?check_suite_focus=true\\     \\ if there were any issues found by the filechecker the ingestion workflow would\\     \\ have stopped\\r\\n** but I think it would be nice to persist the logs of the filechecker,\\     \\ I'll think about something\\r\\n\\r\\n* Link/path to feedback from the depositor\\     \\ (if possible with documented changes that were already done and which not)\\r\\     \\n** same file/file-history as before\\r\\n\\r\\n* Link to the ingestion log.\\r\\n\\     ** ingestions logs are saved in the github-action runs", redmineId: 20352}
  ) {
    id
  }
}



```



4. Ingest DatasetInstance

  - datasetId: 1
  - id: 1
  - locationPath: acdh_resources/container/R_amp_19479
  - state: To be filled in by a harvester