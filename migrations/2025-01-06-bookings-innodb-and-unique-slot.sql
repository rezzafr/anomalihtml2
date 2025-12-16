-- Convert bookings to InnoDB, remove duplicate slots, and enforce uniqueness
START TRANSACTION;

-- Remove any exact duplicate slots, keeping the earliest record id
DELETE b1 FROM bookings b1
JOIN bookings b2
  ON b1.service_id = b2.service_id
 AND b1.booking_date = b2.booking_date
 AND b1.start_time = b2.start_time
 AND b1.end_time = b2.end_time
 AND b1.id > b2.id;

-- Ensure the table supports row-level locking
ALTER TABLE bookings ENGINE=InnoDB;

-- Enforce valid time ordering
ALTER TABLE bookings DROP CHECK IF EXISTS bookings_valid_time;
ALTER TABLE bookings ADD CONSTRAINT bookings_valid_time CHECK (start_time < end_time);

-- Prevent identical time slots for the same service and day
DROP INDEX IF EXISTS uniq_bookings_slot ON bookings;
ALTER TABLE bookings ADD UNIQUE INDEX uniq_bookings_slot (service_id, booking_date, start_time, end_time);

COMMIT;