USE analytic_education;

ALTER TABLE programs
    ADD COLUMN `FullTimeDurationInt` INT NULL AFTER `FullTimeDurationId`,
    ADD COLUMN `PartTimeDurationInt` INT NULL AFTER `PartTimeDurationId`;

/* move existing values to new columns */
/* this shouldn't make sense but we had plain text inputs in the bama form and values entered there ended up in the DurationId fields when they happened to be integers */
UPDATE programs
SET FullTimeDurationInt = FullTimeDurationId,
    PartTimeDurationInt = PartTimeDurationId;