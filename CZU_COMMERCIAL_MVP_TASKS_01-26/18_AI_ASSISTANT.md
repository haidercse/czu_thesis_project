# 18 --- AI Assistant

## Objective

Plan and implement an AI help assistant only after the core application
workflow and data quality are reliable.

## Scope

The assistant may help explain: - Application steps - Checklist items -
Program information - Document preparation concepts - General Czech
university application guidance

## Grounding

Use: 1. Verified internal program data 2. Official university sources 3.
Official Czech government sources where relevant 4. Retrieval-based
context when implementing RAG

## Safety and Accuracy

-   Never invent admission requirements.
-   Never invent deadlines.
-   Never present uncertain information as fact.
-   Clearly distinguish platform data from official-source information.
-   Link/refer students to official sources for final confirmation.
-   Do not guarantee admission, visa approval or residence outcomes.

## Implementation

Start with a narrow, grounded assistant rather than a general chatbot.

## Completion Criteria

The assistant answers only from reliable available context and clearly
handles missing information.
