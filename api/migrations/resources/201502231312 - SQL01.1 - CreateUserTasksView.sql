-- Note: PDO does NOT accept GO statements, and CREATE VIEW must be the first statement in a batch

CREATE VIEW [dbo].[UserTasks_V] AS
SELECT
    u.UserID
    , u.UserName
    , u.EmailAddress
    , r.RoleID
    , r.RoleName
    , t.TaskID
    , t.TaskDescription
FROM dbo.Users u
INNER JOIN dbo.UserRoles ur
    ON u.UserID = ur.UserID
INNER JOIN dbo.Roles r
    ON ur.RoleID = r.RoleID
LEFT OUTER JOIN dbo.RolesTasks rt
    ON r.RoleID = rt.RoleID
LEFT OUTER JOIN dbo.Tasks t
    ON rt.TaskID = t.TaskID
WHERE
        u.[Enabled] = 1
    AND u.Deleted = 0;