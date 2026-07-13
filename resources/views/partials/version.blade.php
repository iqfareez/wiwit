@php
    $releaseTag = config('app.release_tag');
    $commitSha = config('app.commit_sha');
    $refName = config('app.ref_name');
    $repositoryUrl = config('app.repository_url');
@endphp

@if (filled($releaseTag) && filled($repositoryUrl))
    <div
        style="width: 100%; padding: 1rem 1.5rem; color: var(--gray-400); font-size: 14px; line-height: 20px; text-align: center;">
        <a href="{{ rtrim($repositoryUrl, '/') }}/releases/tag/{{ rawurlencode($releaseTag) }}" target="_blank"
            rel="noopener noreferrer" style="color: inherit; text-decoration: none;">
            {{ $releaseTag }}
        </a>
    </div>
@elseif (filled($refName) && filled($commitSha) && filled($repositoryUrl))
    <div
        style="width: 100%; padding: 1rem 1.5rem; color: var(--gray-400); font-size: 14px; line-height: 20px; text-align: center;">
        <a href="{{ rtrim($repositoryUrl, '/') }}/commit/{{ rawurlencode($commitSha) }}" target="_blank"
            rel="noopener noreferrer" style="color: inherit; text-decoration: none;">
            Ver. {{ $refName }}&#64;{{ substr($commitSha, 0, 7) }}
        </a>
    </div>
@endif
