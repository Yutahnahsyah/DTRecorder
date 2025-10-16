INSERT INTO duty_requests (assigned_id, duty_date, time_in, time_out, remarks, status, submitted_at)
VALUES
-- Student 1
(1, '2025-10-14', '08:00:00', '11:00:00', 'Library shelf labeling', 'pending', CURRENT_TIMESTAMP()),
(1, '2025-10-16', '09:30:00', '12:30:00', 'Assisted with book returns', 'pending', CURRENT_TIMESTAMP()),

-- Student 2
(2, '2025-10-15', '10:00:00', '13:00:00', 'Inventory check in science lab', 'pending', CURRENT_TIMESTAMP()),
(2, '2025-10-17', '08:30:00', '11:30:00', 'Helped organize lab equipment', 'pending', CURRENT_TIMESTAMP()),

-- Student 3
(3, '2025-10-14', '13:00:00', '16:00:00', 'Assisted in guidance office filing', 'pending', CURRENT_TIMESTAMP()),
(3, '2025-10-18', '09:00:00', '12:00:00', 'Helped with student ID distribution', 'pending', CURRENT_TIMESTAMP()),

-- Student 4
(4, '2025-10-15', '07:30:00', '10:30:00', 'Cleaned and arranged sports equipment', 'pending', CURRENT_TIMESTAMP()),
(4, '2025-10-19', '10:00:00', '13:00:00', 'Assisted during PE class setup', 'pending', CURRENT_TIMESTAMP()),

-- Student 5
(5, '2025-10-16', '14:00:00', '17:00:00', 'Helped decorate bulletin boards', 'pending', CURRENT_TIMESTAMP()),
(5, '2025-10-20', '08:00:00', '11:00:00', 'Assisted in art room cleanup', 'pending', CURRENT_TIMESTAMP());