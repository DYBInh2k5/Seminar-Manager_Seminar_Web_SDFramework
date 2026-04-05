import './bootstrap';
import React from 'react';
import { createRoot } from 'react-dom/client';
import AiChat from './components/AiChat';
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

const aiChatRoot = document.getElementById('ai-chat-root');

if (aiChatRoot) {
    const endpoint = aiChatRoot.dataset.endpoint ?? '/ai-chat';
    const conversationEndpoint = aiChatRoot.dataset.conversationEndpoint ?? '/ai-chat/conversations';
    const showEndpointTemplate = aiChatRoot.dataset.showEndpointTemplate ?? '/ai-chat/conversations/__CONVERSATION__';
    const bootstrap = JSON.parse(aiChatRoot.dataset.bootstrap ?? '{}');

    createRoot(aiChatRoot).render(
        <React.StrictMode>
            <AiChat
                endpoint={endpoint}
                conversationEndpoint={conversationEndpoint}
                showEndpointTemplate={showEndpointTemplate}
                bootstrap={bootstrap}
            />
        </React.StrictMode>,
    );
}
