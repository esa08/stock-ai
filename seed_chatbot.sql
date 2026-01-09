INSERT INTO conversations (user_id, title, created_at, last_message_at)
VALUES
  (1, 'Budget Plan 2026', '2026-01-01 09:10:00', '2026-01-01 09:35:00'),
  (1, 'Savings Goals', '2026-01-02 10:00:00', '2026-01-02 10:20:00'),
  (1, 'Emergency Fund', '2026-01-03 08:15:00', '2026-01-05 12:45:00'),
  (1, 'Debt Snowball', '2026-01-04 14:30:00', '2026-01-04 15:05:00'),
  (1, 'Monthly Expenses', '2026-01-06 11:05:00', '2026-01-06 11:40:00'),
  (1, 'Investment Basics', '2026-01-08 16:20:00', '2026-01-10 19:00:00'),
  (1, 'Insurance Check', '2026-01-09 13:00:00', '2026-01-09 13:25:00'),
  (1, 'Tax Planning', '2026-01-11 09:45:00', '2026-01-11 10:05:00'),
  (1, 'Side Income Ideas', '2026-01-12 17:10:00', '2026-01-12 17:55:00'),
  (1, 'Retirement Plan', '2026-01-14 08:00:00', '2026-01-15 09:15:00');

INSERT INTO messages (conversation_id, role, content, created_at)
VALUES
  (31, 'user', 'Can you help me plan a weekly budget?', '2026-01-01 09:10:00'),
  (31, 'assistant', 'Sure. Start by listing income and fixed expenses, then set savings targets.', '2026-01-01 09:12:00'),
  (32, 'user', 'How much should I save each month?', '2026-01-02 10:00:00'),
  (32, 'assistant', 'A common guideline is 20% of income, but adjust based on your goals.', '2026-01-02 10:20:00'),
  (33, 'user', 'What is a good emergency fund size?', '2026-01-03 08:15:00'),
  (33, 'assistant', 'Aim for 3 to 6 months of essential expenses.', '2026-01-05 12:45:00'),
  (34, 'user', 'How does the debt snowball method work?', '2026-01-04 14:30:00'),
  (34, 'assistant', 'Pay off the smallest debt first while paying minimums on others.', '2026-01-04 15:05:00'),
  (35, 'user', 'I need help tracking monthly expenses.', '2026-01-06 11:05:00'),
  (35, 'assistant', 'Group expenses into categories and review them weekly.', '2026-01-06 11:40:00'),
  (36, 'user', 'What are safe investment options for beginners?', '2026-01-08 16:20:00'),
  (36, 'assistant', 'Consider diversified index funds or balanced mutual funds.', '2026-01-10 19:00:00'),
  (37, 'user', 'Do I really need insurance?', '2026-01-09 13:00:00'),
  (37, 'assistant', 'Insurance protects you from large unexpected losses.', '2026-01-09 13:25:00'),
  (38, 'user', 'Any tips for tax planning?', '2026-01-11 09:45:00'),
  (38, 'assistant', 'Track deductible expenses and set aside a tax reserve monthly.', '2026-01-11 10:05:00'),
  (39, 'user', 'How can I earn extra income?', '2026-01-12 17:10:00'),
  (39, 'assistant', 'Try freelancing, tutoring, or selling digital products.', '2026-01-12 17:55:00'),
  (40, 'user', 'How should I prepare for retirement?', '2026-01-14 08:00:00'),
  (40, 'assistant', 'Contribute regularly and choose a long-term diversified portfolio.', '2026-01-15 09:15:00');
