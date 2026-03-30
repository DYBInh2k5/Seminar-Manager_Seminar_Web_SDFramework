import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import DashboardAnalytics from './components/DashboardAnalytics';

const dashboardRoot = document.getElementById('dashboard-analytics-root');

if (dashboardRoot) {
    const dashboardData = JSON.parse(dashboardRoot.dataset.dashboard ?? '{}');
    const showLeaderboard = JSON.parse(dashboardRoot.dataset.showLeaderboard ?? 'false');

    createRoot(dashboardRoot).render(
        <React.StrictMode>
            <DashboardAnalytics
                statusBreakdown={dashboardData.statusBreakdown ?? {}}
                roleBreakdown={dashboardData.roleBreakdown ?? {}}
                topLecturers={dashboardData.topLecturers ?? []}
                showLeaderboard={showLeaderboard}
            />
        </React.StrictMode>,
    );
}
